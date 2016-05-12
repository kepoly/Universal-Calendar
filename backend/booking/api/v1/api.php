<?php

include_once "../../calendar/controller.php";

require_once "rest.php";

class API extends REST
{

    private $data = "";


    public function __construct()
    {
        parent::__construct();
        $connect = "";
        $this->data = new CalendarController($connect);
        $this->returnCalendarData(true);
    }

    /*
     * Process the api any incoming vars strip slashes
     * Check if the method exists
     * If so run the method, else return 404.
     */
    public function processApi()
    {

        if (isset($_REQUEST['x'])) {
            $func = strtolower(trim(str_replace("/", "", $_REQUEST['x'])));
            if ((int)method_exists($this, $func) > 0) {
                $this->$func();
            } else {
                $this->response('Error: Endpoint does not exist.', 404);
            }
        } else {
            $this->response('CSCGaming Blackjack API V1', 404);
        }

        // If the method not exist with in this class "Page not found".
    }

    public function returnCalendarData($inside = false)
    {

        if(isset($this->_request['day'])) {
            $day = $this->_request['day'];
        } else {
            $day = 01;
        }

        if(isset($this->_request['month'])) {
            $month = $this->_request['month'];
        } else {
            $month = date("m");
        }

        if(isset($this->_request['year'])) {
            $year = $this->_request['year'];
        } else {
            $year = date("Y");
        }

        // Unix Timestamp of the date a user has clicked on
        $selected_date = mktime(0, 0, 0, $month, 01, $year);

        // Unix Timestamp of the previous month which is used to give the back arrow the correct month and year
        $back = strtotime("-1 month", $selected_date);

        // Unix Timestamp of the next month which is used to give the forward arrow the correct month and year
        $forward = strtotime("+1 month", $selected_date);

        if($inside) {
            $this->data->createCalendar($selected_date, $back, $forward, $day, $month, $year);
        }

        $returnArray = array();
        $returnArray["calendarDays"] = $this->data->days;
        $returnArray["calendarHeader"] = $this->returnCalendarHeader(true);
        $returnArray["calendarCells"] = $this->returnCalendarCells(true);
        $returnArray["bookingForm"] = $this->returnBookingForm(true);
        if ($inside) {
            $returnArray = array();
            return null;
        } else {
            $this->response($this->json($returnArray), 200); // send user details
        }

    }


    public function returnCalendarHeader($inside = false)
    {

        $returnArray = array();
        $returnArray["backMonthUrl"] = "month=" . date("m", $this->data->back);
        $returnArray["backMonthUrl"] .= "&year=" . date("Y", $this->data->back);
        $returnArray["currentMonth"] = date("F, Y", $this->data->selected_date);
        $returnArray["frontMonthUrl"] = "month=" . date("m", $this->data->forward);
        $returnArray["frontMonthUrl"] .= "&year=" . date("Y", $this->data->forward);

        $returnArray["daysOfWeek"] = array();
        $count = 0;

        foreach ($this->data->day_order as $r) {

            switch ($this->data->day_header_format) {

                case(1):
                    $returnArray["daysOfWeek"][$count] = substr($r, 0, 1);
                    break;

                case(2):
                    $returnArray["daysOfWeek"][$count] = substr($r, 0, 3);
                    break;

                case(3):
                    $returnArray["daysOfWeek"][$count] = $r;
                    break;

            } // Close switch
            $count++;

        } // Close foreach

        if($inside) {
            return $returnArray;
        } else {
            $this->response($this->json($returnArray), 200); // send user details
        }
    }

    public function returnCalendarCells($inside = false)
    {

        $returnArray = array();
        $count = 0;


        if(isset($this->_request['day'])) {
            $this->data->day = $this->_request['day'];
        } else {
            $this->data->day = 01;
        }

        if(isset($this->_request['month'])) {
            $this->data->month = $this->_request['month'];
        } else {
            $this->data->month = date("m");
        }

        if(isset($this->_request['year'])) {
            $this->data->year = $this->_request['year'];
        } else {
            $this->data->year = date("Y");
        }

//        $returnArray[$count]["dayClosed"] = "false";
//        $returnArray[$count]["isDayUnavailable"] = "false";
//        $returnArray[$count]["isDayInPast"] = "false";
//        $returnArray[$count]["dayNumber"] = 0;

        foreach ($this->data->days as $i => $r) { // Loop through the date array

            $tag = 0;

            if (in_array($r['dayname'], $this->data->days_closed)) {
                $returnArray[$count]["dayClosed"] = "true";
                $tag = 1;
            }

            if (mktime(0, 0, 0, $this->data->month, sprintf("%02s", $r['daynumber']) + 1, $this->data->year) < strtotime("now") && $tag != 1) {
                $returnArray[$count]["isDayInPast"] = "true";
                $returnArray[$count]["dayNumber"] = $r["daynumber"];
                $tag = 1;
            }

            if ($r['dayname'] == 'blank' && $tag != 1) {
                $returnArray[$count]["isDayUnavailable"] = "true";
                $tag = 1;
            }

            // Now check the booking array $this->booking to see whether we have a booking on this day
            $current_day = $this->data->year . '-' . $this->data->month . '-' . sprintf("%02s", $r['daynumber']);

            if (isset($this->data->bookings_per_day[$current_day]) && $tag == 0) {

                $current_day_slots_booked = count($this->data->bookings_per_day[$current_day]);

                if ($current_day_slots_booked < $this->data->slots_per_day) {
                    $returnArray[$count]["calendarDayUrl"] = "month=" . $this->data->month;
                    $returnArray[$count]["calendarDayUrl"] .= "&year=" . $this->data->year;
                    $returnArray[$count]["calendarDayUrl"] .= "&day=" . sprintf("%02s", $r['daynumber']);
                    $returnArray[$count]["calendarDayStatus"] = "part_booked";
                    $returnArray[$count]["calendarDayNumber"] = $r['daynumber'];
                    $tag = 1;
                } else {
                    $returnArray[$count]["calendarDayUrl"] = "month=" . $this->data->month;
                    $returnArray[$count]["calendarDayUrl"] .= "&year=" . $this->data->year;
                    $returnArray[$count]["calendarDayUrl"] .= "&day=" . sprintf("%02s", $r['daynumber']);
                    $returnArray[$count]["calendarDayStatus"] = "fully_booked";
                    $returnArray[$count]["calendarDayNumber"] = $r['daynumber'];
                    $tag = 1;
                }
            }

            if ($tag == 0) {
                $returnArray[$count]["calendarDayUrl"] = "month=" . $this->data->month;
                $returnArray[$count]["calendarDayUrl"] .= "&year=" . $this->data->year;
                $returnArray[$count]["calendarDayUrl"] .= "&day=" . sprintf("%02s", $r['daynumber']);
                $returnArray[$count]["calendarDayStatus"] = "open_bookings";
                $returnArray[$count]["calendarDayNumber"] = $r['daynumber'];
            }
            $count++;
        }
        if($inside) {
            return $returnArray;
        } else {
            $this->response($this->json($returnArray), 200); // send user details
        }
    }


    public function shouldProcessForm()
    {

        $returnArray = array();
        $returnArray["shouldReturnForm"] = "false";
        $current_day = $this->data->year . '-' . $this->data->month . '-' . $this->data->day;
        $slots_selected_day = 0;

        if (isset($this->data->bookings_per_day[$current_day])) {
            $slots_selected_day = count($this->data->bookings_per_day[$current_day]);
        }

        if ($this->data->day != 01 && $slots_selected_day < $this->data->slots_per_day) {
            $returnArray["shouldReturnForm"] = "true";
        }
        $this->response($this->json($returnArray), 200); // send user details
    }

    public function returnBookingForm($inside = false)
    {
        $returnArray = array();
        $count = 0;

        if(isset($this->_request['day'])) {
            $this->data->day = $this->_request['day'];
        } else {
            $this->data->day = 01;
        }

        if(isset($this->_request['month'])) {
            $this->data->month = $this->_request['month'];
        } else {
            $this->data->month = date("m");
        }

        if(isset($this->_request['year'])) {
            $this->data->year = $this->_request['year'];
        } else {
            $this->data->year = date("Y");
        }


        $returnArray[$count]["availableSlots"] = $this->data->day . "-" . $this->data->month . "-" . $this->data->year;


        // Create $slots array of the booking times
        for ($i = strtotime($this->data->booking_start_time); $i <= strtotime($this->data->booking_end_time); $i = $i + $this->data->booking_time_frequency * 60) {
            $slots[] = date("H:i:s", $i);
        }

        // Loop through $this->bookings array and remove any previously booked slots
        if ($this->data->is_slot_booked_today == 1) { // $this->is_slot_booked_today created in function 'make_booking_array'

            foreach ($this->data->bookings as $i => $b) {

                if ($b['date'] == $this->data->year . '-' . $this->data->month . '-' . $this->data->day) {

                    // Remove any booked slots from the $slots array
                    $slots = array_diff($slots, array($b['start']));

                } // Close if

            } // Close foreach

        } // Close if
        $count++;
        foreach ($slots as $i => $start) {
            // Calculate finish time
            $finish_time = strtotime($start) + $this->data->booking_time_frequency * 60;
            $returnArray[$count]["startTime"] = $start;
            $returnArray[$count]["endTime"] = date("H:i:s", $finish_time);
            $returnArray[$count]["costForSlot"] = number_format($this->data->cost_per_time_frequency, 2);
            $returnArray[$count]["checkBoxValue"] = $start . " - " . date("H:i:s", $finish_time);
            $count++;
        }

        if($inside) {
            return $returnArray;
        } else {
            $this->response($this->json($returnArray), 200); // send user details

        }
    }

    public function returnBasketAndCheckOut()
    {
        $returnArray = array();

        if ($this->get_request_method() != "GET") {

        }

        if(isset($this->_request['day'])) {
            $day = $this->_request['day'];
        } else {
            $day = $this->data->day;
        }

        if(isset($this->_request['month'])) {
            $month = $this->_request['month'];
        } else {
            $month = $this->data->month;
        }

        if(isset($this->_request['year'])) {
            $year = $this->_request['year'];
        } else {
            $year = $this->data->year;
        }


        // Validate GET date values
        if (checkdate($month, $day, $year) !== false) {
            $selected_day = $year . '-' . $month . '-' . $day;
        } else {

        }

        $returnArray["costPerSlot"] = $this->data->cost_per_time_frequency;
        $returnArray["bookingDate"] = $year . '-' . $month . '-' . $day;

        $this->response($this->json($returnArray), 200); // send user details
    }

    /*
     *	Encode array into JSON
     */
    private function json($data)
    {
        if (is_array($data)) {
            return json_encode($data, JSON_UNESCAPED_SLASHES);
        }
    }

}

// Initiiate Library

$api = new API;
$api->processApi();
