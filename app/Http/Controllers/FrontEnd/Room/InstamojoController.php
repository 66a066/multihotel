<?php

namespace App\Http\Controllers\FrontEnd\Room;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Room\RoomBookingController;
use App\Http\Helpers\Instamojo;
use App\Models\Commission;
use App\Models\Earning;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\RoomManagement\Room;
use App\Models\RoomManagement\RoomBooking;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Traits\MiscellaneousTrait;
use Exception;
use Illuminate\Http\Request;

class InstamojoController extends Controller
{
  use MiscellaneousTrait;

  private $api;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('instamojo')->first();
    $instamojoData = json_decode($data->information, true);

    if ($instamojoData['sandbox_status'] == 1) {
      $this->api = new Instamojo($instamojoData['instamojo_key'], $instamojoData['instamojo_token'], 'https://test.instamojo.com/api/1.1/');
    } else {
      $this->api = new Instamojo($instamojoData['instamojo_key'], $instamojoData['instamojo_token']);
    }
  }

  public function bookingProcess(Request $request)
  {
    $roomBooking = new RoomBookingController();

    // do calculation
    $calculatedData = $roomBooking->calculation($request);

    $title = 'Room Booking';

    $currencyInfo = MiscellaneousTrait::getCurrencyInfo();

    // checking whether the currency is set to 'INR' or not
    if ($currencyInfo->base_currency_text !== 'INR') {
      return redirect()->back()->with('error', 'Invalid currency for instamojo payment.');
    }

    $information['subtotal'] = $calculatedData['subtotal'];
    $information['discount'] = $calculatedData['discount'];
    $information['total'] = $calculatedData['total'];
    $information['currency_symbol'] = $currencyInfo->base_currency_symbol;
    $information['currency_symbol_position'] = $currencyInfo->base_currency_symbol_position;
    $information['currency_text'] = $currencyInfo->base_currency_text;
    $information['currency_text_position'] = $currencyInfo->base_currency_text_position;
    $information['method'] = 'Instamojo';
    $information['type'] = 'online';

    // store the room booking information in database
    $booking_details = $roomBooking->storeData($request, $information);

    $notify_url = route('room_booking.instamojo.notify');

    try {
      $response = $this->api->paymentRequestCreate(array(
        'purpose' => $title,
        'amount' => $calculatedData['total'],
        'buyer_name' => $booking_details->customer_name,
        'email' => $booking_details->customer_email,
        'send_email' => false,
        'phone' => $booking_details->customer_phone,
        'send_sms' => false,
        'redirect_url' => $notify_url
      ));

      // put some data in session before redirect to instamojo url
      session()->put('bookingId', $booking_details->id);   // db row number
      session()->put('paymentId', $response['id']);

      return redirect($response['longurl']);
    } catch (Exception $e) {
      return redirect()->back()->with('error', 'Sorry, transaction failed!');
    }
  }

  public function notify(Request $request)
  {
    // get the information from session
    $bookingId = session()->get('bookingId');
    $paymentId = session()->get('paymentId');

    $urlInfo = $request->all();

    if ($urlInfo['payment_request_id'] == $paymentId) {
      // update the payment status for room booking in database
      $bookingInfo = RoomBooking::where('id', $bookingId)->first();

      $bookingInfo->update(['payment_status' => 1]);

      $roomBooking = new RoomBookingController();

      // generate an invoice in pdf format
      $invoice = $roomBooking->generateInvoice($bookingInfo);

      $room = Room::where('id', $bookingInfo->room_id)->first();
      if (!empty($room)) {
        if ($room->vendor_id != NULL) {
          $vendor_id = $room->vendor_id;
        } else {
          $vendor_id = NULL;
        }
      } else {
        $vendor_id = NULL;
      }

      //calculate commission
      $percent = Commission::select('room_booking_commission')->first();

      $commission = (($bookingInfo->grand_total) * $percent->room_booking_commission) / 100;

      //get vendor
      $vendor = Vendor::where('id', $vendor_id)->first();

      //add blance to admin revinue
      $earning = Earning::first();

      $earning->total_revenue = $earning->total_revenue + $bookingInfo->grand_total;
      if ($vendor) {
        $earning->total_earning = $earning->total_earning + $commission;
      } else {
        $earning->total_earning = $earning->total_earning + $bookingInfo->grand_total;
      }
      $earning->save();

      //store Balance  to vendor
      if ($vendor) {
        $pre_balance = $vendor->amount;
        $vendor->amount = $vendor->amount + ($bookingInfo->grand_total - ($commission));
        $vendor->save();
        $after_balance = $vendor->amount;

        $received_amount = ($bookingInfo->grand_total - ($commission));

        // then, update the invoice field info in database
        $bookingInfo->update([
          'invoice' => $invoice,
          'comission' => $commission,
          'received_amount' => $received_amount,
        ]);
      } else {
        // then, update the invoice field info in database
        $bookingInfo->update([
          'invoice' => $invoice
        ]);
        $received_amount = $bookingInfo->grand_total;
        $after_balance = NULL;
        $pre_balance = NULL;
      }
      $data = [
        'transcation_id' => time(),
        'booking_id' => $bookingInfo->id,
        'transcation_type' => 1,
        'user_id' => null,
        'vendor_id' => $vendor_id,
        'payment_status' => 1,
        'payment_method' => $bookingInfo->payment_method,
        'grand_total' => $bookingInfo->grand_total,
        'commission' => $bookingInfo->comission,
        'pre_balance' => $pre_balance,
        'after_balance' => $after_balance,
        'gateway_type' => $bookingInfo->gateway_type,
        'currency_symbol' => $bookingInfo->currency_symbol,
        'currency_symbol_position' => $bookingInfo->currency_symbol_position,
      ];
      store_transaction($data);

      // send a mail to the customer with an invoice
      $roomBooking->sendMail($bookingInfo);

      // remove all session data
      session()->forget('bookingId');
      session()->forget('paymentId');

      return redirect()->route('room_booking.complete');
    } else {
      return redirect()->route('room_booking.cancel');
    }
  }
}
