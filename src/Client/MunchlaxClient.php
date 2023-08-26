<?php

declare(strict_types=1);

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
        $skipCategories = ['Go Battle League', 'Research Break'];

        $crawler = new Crawler((string) file_get_contents('https://www.munchlax.nl/'));

        $liveEvents = $crawler->filter('.card-live a')->each(function (Crawler $node) use ($skipTitles, $skipCategories) {
            $liveEventUrl = 'https://www.munchlax.nl/'.$node->attr('href');
            $liveEventCrawler = new Crawler((string) file_get_contents($liveEventUrl));
            $title = $liveEventCrawler->filter('h1')->innerText();
            foreach ($skipTitles as $skipTitle) {
                if (str_contains($title, $skipTitle)) {
                    return null;
                }
            }
            $category = $liveEventCrawler->filter('.badge-primary')->innerText();
            foreach ($skipCategories as $skipCategory) {
                if (strtolower($skipCategory) === strtolower($category)) {
                    return null;
                }
            }

            $dates = $liveEventCrawler->filter('.sub')->innerText();
            [$dateStartString, $dateEndString] = explode(' → ', trim($dates, '()'));
            $dateTimeZone = new \DateTimeZone('Europe/Amsterdam');

            /** @var \DateTimeImmutable $dateStart */
            $dateStart = \DateTimeImmutable::createFromFormat('d-m-y H:i', $dateStartString, $dateTimeZone);

            /** @var \DateTimeImmutable $dateEnd */
            $dateEnd = \DateTimeImmutable::createFromFormat('d-m-y H:i', $dateEndString, $dateTimeZone);

            return new CalendarItem(
                (string) Uuid::v6(),
                $title,
                $liveEventUrl,
                $dateStart,
                $dateEnd
            );
        });

        $otherEvents = $crawler->filter('.list-group-item')->each(function (Crawler $node) use ($skipTitles, $skipCategories) {
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
            [$dateStartString, $dateEndString] = explode(' → ', trim($dates, '()'));
            $dateTimeZone = new \DateTimeZone('Europe/Amsterdam');
            $link = 'https://www.munchlax.nl/'.$node->filter('a')->attr('href');

            /** @var \DateTimeImmutable $dateStart */
            $dateStart = \DateTimeImmutable::createFromFormat('d-m-Y H:i', $dateStartString, $dateTimeZone);

            /** @var \DateTimeImmutable $dateEnd */
            $dateEnd = \DateTimeImmutable::createFromFormat('d-m-Y H:i', $dateEndString, $dateTimeZone);

            return new CalendarItem(
                (string) Uuid::v6(),
                $title,
                $link,
                $dateStart,
                $dateEnd
            );
        });

        $events = array_merge($liveEvents, $otherEvents);
        $events = array_filter($events);

        return new Calendar('Pokemon Go', $events);
    }
}
