<?php

namespace Endroid\Pokemon\Client;

use Endroid\Calendar\Model\Calendar;
use Endroid\Calendar\Model\CalendarItem;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Uid\Uuid;

final class MunchlaxClient
{
    public function getCalendar(): Calendar
    {
        $crawler = new Crawler((string) file_get_contents('https://www.munchlax.nl/'));
        $events = $crawler->filter('.list-group-item')->each(function (Crawler $node) {
            $title = $node->filter('.title')->innerText();
            $dates = $node->filter('.footer')->innerText();
            [$dateStart, $dateEnd] = explode(' → ', trim($dates, '()'));

            return new CalendarItem(
                Uuid::v6(),
                $title,
                '',
                new \DateTimeImmutable($dateStart),
                new \DateTimeImmutable($dateEnd)
            );
        });

        return new Calendar('Pokemon Go', $events);
    }
}
