<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;

class StudentController extends Controller
{
    /**
     * @param $startDate
     * @param $daysPerWeek
     * @param $sessions
     * @return mixed
     */
    public function bookSchedule($startDate, $daysPerWeek, $sessions)
    {
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $startDate) || !$startDate) {
            return response()->json([
                'success' => 'false',
                'message' => 'error date should like yyyy-mm-dd'
            ]);
        } elseif (is_array($daysPerWeek)) {
            return response()->json([
                'success' => 'false',
                'message' => 'error days per week should be an array'
            ]);
        } elseif (!is_numeric($sessions) || $sessions > 7 || $sessions < 1) {
            return response()->json([
                'success' => 'false',
                'message' => 'error sessions must be integer and between 1 and 7'
            ]);
        }
        //Convert date to array
        $arrayOfStartDate = explode('-', $startDate);
        //convert array to int
        $daysPerWeek = json_decode($daysPerWeek, true);
        //take input date
        $date = Carbon::create($arrayOfStartDate[0], $arrayOfStartDate[1], $arrayOfStartDate[2])->locale('ar');
        //calculate all sessions
        //30 is number of chapters
        $totalSessions = 30 * $sessions;
        //array to store dates
        $calendarOfSessions = [];
        //counter to know last session date
        $counter = 0;
        //calculate number of loops
        $loop = ceil($totalSessions / count($daysPerWeek));
        for ($i = 1; $i <= $loop; $i++) {

            for ($y = 0; $y < count($daysPerWeek); $y++) {
                if ($totalSessions <= $counter) {
                    break;
                }
                //get date using start of week
                $calendarOfSessions[] = $date->startOfWeek()->addDays($daysPerWeek[$y] - 1)->format('Y-m-d');
                $counter++;
            }
            //mve to next week
            $date->addDays(7);
        }
        //fail
        if (!$calendarOfSessions) {
            $response = [
                'success' => 'false',
                'message' => 'Error'
            ];
            return response()->json([$response, 404]);
        }
        //success
        $response = [
            'success' => 'true',
            'message' => 'Data loaded successfully',
            'data' => $calendarOfSessions
        ];
        return response()->json([$response, 200]);

    }
}

