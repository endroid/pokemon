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
        $skipTitles = ['Season ', ' Special Research'];
        $skipCategories = ['Go Battle League', 'Research Breakthrough', 'Research Breaktrough'];

        $crawler = new Crawler((string) file_get_contents('https://www.munchlax.nl/'));
        $events = $crawler->filter('.list-group-item')->each(function (Crawler $node) use ($skipTitles, $skipCategories) {
            $title = $node->filter('.title')->innerText();
            foreach ($skipTitles as $skipTitle) {
                if (str_contains($title, $skipTitle)) {
                    return null;
                }
            }
            $category = $node->filter('.badge-primary')->innerText();
            foreach ($skipCategories as $skipCategory) {
                if (strtolower($skipCategory) === strtolower($category)) {
                    return null;
                }
            }
            $dates = $node->filter('.footer')->innerText();
            [$dateStart, $dateEnd] = explode(' → ', trim($dates, '()'));
            $dateTimeZone = new \DateTimeZone('Europe/Amsterdam');

            return new CalendarItem(
                Uuid::v6(),
                $title,
                '',
                (new \DateTimeImmutable($dateStart, $dateTimeZone)),
                (new \DateTimeImmutable($dateEnd, $dateTimeZone))
            );
        });

        $events = array_filter($events);

        return new Calendar('Pokemon Go', $events);
    }
}
