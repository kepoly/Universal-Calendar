<?php
/**
 * Created by PhpStorm.
 * User: kepoly
 * Date: 5/11/2016
 * Time: 1:28 AM
 */

class CalendarController
{

    public $booking_start_time = "08:00"; //start time of the day in 24 hr format
    public $booking_end_time = "20:00"; //end of the day in 24 hr format

    //this can be changed to accept a value-----------------------------------------
    public $booking_time_frequency = 30; //length of each slot available for booking

    public $day_header_format = 2; // Day format of the table header.  Possible values (1, 2, 3)
    // 1 = Show First digit, eg: "M"
    // 2 = Show First 3 letters, eg: "Mon"
    // 3 = Full Day, eg: "Monday"

    public $days_closed = array();
    public $day_closed_text = "CLOSED";

    public $cost_per_time_frequency = 185.00; //the cost for each length of the booking_time_frequency
    public $currency_tag = "&#x24;"; //currency tag to be used

    //variables to be used further to construct the calendar
    public $day;
    public $month;
    public $year;

    public $back;
    public $back_month;
    public $back_year;
    public $forward;
    public $forward_month;
    public $forward_year;

    public $selected_date;
    public $bookings;
    public $count;
    public $days;
    public $is_slot_booked_today;
    public $slots_per_day;
    public $bookings_per_day;


    public $day_order = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

    function __construct($connect)
    {

        // Make a MySQL Connection
        $host="localhost";
        $user="root";
        $password="";
        $db = "booking";

        $link = mysqli_connect($host, $user, $password);
        mysqli_select_db($link, $db) or die(mysqli_error($link));

        $this->dbConnect = $link;
    }

    public function createCalendar($selected_date, $back, $forward, $day, $month, $year)
    {

        //if we have a get variable in the current url we will recieve the data if not we set default data on the other page
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;

        // $back and $forward are Unix Timestamps of the previous / next month, used to give the back arrow the correct month and year
        $this->selected_date = $selected_date;
        $this->back = $back;
        $this->back_month = date("m", $back);
        $this->back_year = date("Y", $back); // Minus one month back arrow

        $this->forward = $forward;
        $this->forward_month = date("m", $forward);
        $this->forward_year = date("Y", $forward); // Add one month forward arrow

        $this->createBookingArray($year, $month);

    }

    public function createBookingArray($year, $month, $var = 0)
    {

        //pull the current bookings for this year->month from the database
        $query = "SELECT * FROM bookings WHERE date LIKE '$year-$month%'";
        $result = mysqli_query($this->dbConnect, $query) or die(mysqli_error($this->dbConnect));
        $this->is_slot_booked_today = 0; // Defaults to 0

        //if there is bookings create an array to handle the booked dates
        while ($row = mysqli_fetch_array($result)) {

            //array to be used later
            $this->bookings_per_day[$row['date']][] = $row['start'];

            //bookings array containing current bookings for year->month
            $this->bookings[] = array(
                "name" => $row['name'],
                "date" => $row['date'],
                "start" => $row['start']
            );

        }

        $this->slots_per_day = 0;
        //calculate how many slots there are per day to be used later during creation of slot table
        for($i = strtotime($this->booking_start_time); $i <= strtotime($this->booking_end_time); $i = $i + $this->booking_time_frequency * 60) {
            $this->slots_per_day++;
        }

        $this->createDaysArray($year, $month);
    }

    public function createDaysArray($year, $month) {

        // Calculate the number of days in the selected month
        $num_days_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Make $this->days array containing the Day Number and Day Number in the selected month

        for($i = 1; $i <= $num_days_month; $i++) {

            // Work out the Day Name ( Monday, Tuesday... ) from the $month and $year variables
            $d = mktime(0, 0, 0, $month, $i, $year);

            // Create the array
            $this->days[] = array("daynumber" => $i, "dayname" => date("l", $d));

        }
        /*
        Sample output of the $this->days array:

        [0] => Array
            (
                [daynumber] => 1
                [dayname] => Monday
            )

        [1] => Array
            (
                [daynumber] => 2
                [dayname] => Tuesday
            )
        */

        $this->createCalendarBlankStartDays();
        $this->createCalendarBlankEndDays();
    }

    public function createCalendarBlankStartDays() {

        /*
        Calendar months start on different days
        Therefore there are often blank 'unavailable' days at the beginning of the month which are showed as a grey block
        The code below creates the blank days at the beginning of the month
        */

        // Get first record of the days array which will be the First Day in the month ( eg Wednesday )
        $first_day = $this->days[0]['dayname'];
        $s = 0;

        // Loop through $day_order array ( Monday, Tuesday ... )
        foreach ($this->day_order as $i => $r) {

            // Compare the $first_day to the Day Order
            if ($first_day == $r && $s == 0) {

                $s = 1;  // Set flag to 1 stop further processing

            } elseif ($s == 0) {

                //because the if says the day is blank create blank day
                $blank = array(
                    "daynumber" => 'blank',
                    "dayname" => 'blank'
                );

                // Prepend/add and push elements to the beginning of the $day array
                array_unshift($this->days, $blank);
            }

        } // Close foreach

    }

    public function createCalendarBlankEndDays() {

        /*
        Calendar months start on different days
        Therefore there are often blank 'unavailable' days at the end of the month which are showed as a grey block
        The code below creates the blank days at the end of the month
        */

        // Add blank elements to end of array if required.
        $pad_end = 7 - (count($this->days) % 7);

        if ($pad_end < 7) {

            $blank = array(
                "daynumber" => 'blank',
                "dayname" => 'blank'
            );

            for ($i = 1; $i <= $pad_end; $i++) {
                array_push($this->days, $blank);
            }

        } // Close if

        $this->createCalendarHeader();

    }

    public function createCalendarHeader() {



    }



}












