<?php
    /* @var $this EventController */
    /* @var $events array */
    /* @var $eventCounts array */
    /* @var $year string */
    /* @var $month string */
    /* @var $city City */

    $selectedDatetime = mktime(0, 0, 0, $month, 1, $year);
    $lastDayOfMonthDatetime = mktime(0, 0, 0, $month, date('t', $selectedDatetime), $year);

    $firstWeekOfMonth = date('W', $selectedDatetime);

    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $week_day = date('N', mktime(0, 0, 0, $month, 1, $year));
    $weeksCount = ceil(($days + $week_day) / 7);
?>
<table id="colen" colspan="0" rowspan="0">
    <tr>
        <th colspan="7">
            <?php print CHtml::encode($city->name).', '.Yii::app()->locale->getMonthName($month, 'wide', true).' '.$year; ?>
        </th>
    </tr>
    <tr class="day">
        <td><?php print Yii::t('application', 'ПН'); ?></td>
        <td><?php print Yii::t('application', 'ВТ'); ?></td>
        <td><?php print Yii::t('application', 'СР'); ?></td>
        <td><?php print Yii::t('application', 'ЧТ'); ?></td>
        <td><?php print Yii::t('application', 'ПТ'); ?></td>
        <td><?php print Yii::t('application', 'СБ'); ?></td>
        <td><?php print Yii::t('application', 'ВС'); ?></td>
    </tr>
    <?php for($i = 0; $i < $weeksCount; $i++): ?>
            <tr>
                <?php $daysOfWeek = EventHelper::getDaysOfWeek($firstWeekOfMonth + $i, $year); ?>
                <?php foreach($daysOfWeek as $day): ?>
                    <?php
                    $tdClass = '';
                    if((date('n', strtotime($day)) != $month))
                    {
                        $tdClass = 'old_m';
                    }
                    else
                    {
                        if(isset($eventCounts['global'][$day]) && $eventCounts['global'][$day])
                        {
                            $tdClass = 'day_red';
                        }
                        elseif(isset($eventCounts['mine'][$day]) && $eventCounts['mine'][$day])
                        {
                            $tdClass = 'day_blue2';
                        }
                        elseif(isset($eventCounts['subscribed'][$day]) && $eventCounts['subscribed'][$day])
                        {
                            $tdClass = 'day_blue';
                        }
                        elseif(date('Y-m-d') == $day)
                        {
                            $tdClass .= ' day_gray';
                        }
                    }
                    ?>
                    <td id="calendar-<?php print $day; ?>" style="cursor: pointer;" class="calendarDay <?php print $tdClass; ?>"><?php print date('j', strtotime($day)); ?>

                        <div class="wind_com" style="display: none; min-width: 200px;">
                            <div class="ico_uk"></div>
                            <?php if(isset($events[$day])): ?>
                                <ul>
                                    <?php foreach($events[$day] as $item): ?>
                                        <li>
                                            <p>
                                                <a class="event-link" href="<?php print $this->createUrl('event/detail', array('eventId' => $item['eventId'])); ?>">
                                                    <?php print CHtml::encode($item['name']); ?>
                                                </a>
                                            </p>
                                            <div class="date"><?php print $item['dateStart']; ?>  |  <?php print $item['timeStart']; ?></div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p style="color: #000; text-align: center;" ><?php print Yii::t('application', 'Мероприятий нет'); ?></p>
                            <?php endif; ?>
                            <a href="<?php print $this->createUrl('event/add'); ?>" class="event-link add_mir"><?php print Yii::t('application', 'Добавить мероприятие'); ?></a>
                        </div>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
</table>