<?php
/**
 * Created by PhpStorm.
 * User: kepoly
 * Date: 5/12/2016
 * Time: 12:53 AM
 */


?>
<div class="text-center calendarWrapperWrap">

<div class="text-center calendarWrapper">

<div class="calendarHeader">
    <div class="col-md-2">
        <button class="btn btn-default" ng-click="changeMonth(calendarHeader.backMonthUrl)">Back</button>
    </div>
    <div class="col-md-8">
        {{calendarHeader.currentMonth}}
    </div>

    <div class="col-md-2">
        <button class="btn btn-default" ng-click="changeMonth(calendarHeader.frontMonthUrl)">Forward</button>
    </div>

</div>


    <div class="daysOfWeek">
        <div class="dayOfWeekHeader" ng-repeat="data in calendarHeader.daysOfWeek">
            {{data}}
        </div>
    </div>


    <div class="clearfix">

    </div>

<div class="bookingCalendarDayBox" ng-class="{dayBoxGrey: data.daynumber==='blank', dayBoxGreen: data.daynumber != 'blank'}" ng-repeat="data in calendarDays track by $index">

    <div ng-if="calendarCells[$index].isDayInPast === undefined" ng-click="getFormWithData(calendarCells[$index].calendarDayUrl)" class="{{calendarCells[$index].calendarDayStatus}}">

            <div ng-if="data.daynumber != 'blank'">
                {{data.daynumber}}
            </div>
                {{data.dayname}}
        </div>

    <div ng-if="calendarCells[$index].isDayInPast !== undefined">
        <div ng-if="calendarCells[$index].dayNumber === 'blank'">
        </div>
        <div ng-if="calendarCells[$index].dayNumber !== 'blank'" class="previousDay">
            {{data.daynumber}}<br />
            Previous Day
        </div>

</div>


</div>

    <div ng-if="showBookingForm === true">


        <div class="" ng-repeat="data in bookingFormData | startFrom: 1">

            <div class="col-sm-3">
                <p>Start Time</p>
                <p>{{data.startTime}}</p>
            </div>
            <div class="col-sm-3">
                <p>End Time</p>
                <p>{{data.endTime}}</p>
            </div>
            <div class="col-sm-3">
                <p>Price for Slot</p>
                <p>{{data.costForSlot}}</p>
            </div>
            <div class="col-sm-3">
                <p>Book Slot</p>
                <input type="checkbox" class="form-control" value="{{data.checkBoxValue}}" />
            </div>
        </div>


    </div>



</div>
</div>