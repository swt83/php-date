<?php

echo '<table class="calendar">';
    echo '<thead>';
        echo '<tr>';
            echo '<th>S</th>';
            echo '<th>M</th>';
            echo '<th>T</th>';
            echo '<th>W</th>';
            echo '<th>T</th>';
            echo '<th>F</th>';
            echo '<th>S</th>';
        echo '</tr>';
    echo '<thead>';
    echo '<tbody>';
        $count = 1;
        foreach ($map as $day)
        {
            // open row
            echo $count == 1 ? '<tr>' : '';
            
            // load variables
            $date = $day['date']; // object
            $function = $day['data']; // function

            // print information
            echo '<td class="'.($day['is_disabled'] ? 'disabled ' : ($day['is_today'] ? 'today ' : '')).'">';
                echo '<div class="date">'.$date->format('%e').'</div>';
                echo is_callable($function) ? '<div class="data">'.$function($date).'</div>' : '';
            echo '</td>';

            // close row
            echo $count == 7 ? '</tr>' : '';
            
            // increment
            $count = $count == 7 ? 1 : $count + 1;
        }
    echo '</tbody>';
echo '</table>';