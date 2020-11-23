<?php
require_once "vendor/autoload.php";
try {
    $bot = new \TelegramBot\Api\Client('API_KEY');
    $bot->command('start', function ($message) use ($bot) {
        $answer = "Список команд:\n"."/schedule узнать расписание игр";
        $bot->sendMessage($message->getChat()->getId(),$answer);
    });
    $bot->command('schedule', function ($message) use ($bot) {

      include('simple_html_dom.php');


      $linkHome = 'http://ru.lolesports.com';

      $htmlHome = file_get_html($linkHome);

      $classHome = 'p-home-schedule_show-all';

      $linkScheduleHome = array();

      foreach( $htmlHome -> find('a[class*="'.$classHome.'"]') as $a ) {
       $linkScheduleHome[] = $a->href;
      }

      if((count($linkScheduleHome) == 0) || (count($linkScheduleHome) > 1)) {
       exit('Ошибка! Ссылка на полное расписание не найдена');
      }

      $linkScheduleMain;

      if (substr($linkScheduleHome[0], 0, 1) != '/') {
       $linkScheduleHome[0] = '/'.$linkScheduleHome[0];
       $linkScheduleMain = $linkHome . $linkScheduleHome[0];
      } else {
       $linkScheduleMain = $linkHome . $linkScheduleHome[0];
      }

      /**************************************************/

      $htmlScheduleMain = file_get_html($linkScheduleMain);


      $headerBlock; // Содержит заголовк и табы с датами

      foreach($htmlScheduleMain->find('div[class="p-schedule_header-container"]') as $hb) {
      $headerBlock = $hb;
      }

      $contentBlock;

      foreach($htmlScheduleMain->find('div[class="p-schedule_content"]') as $cb) {
      $contentBlock = $cb;
      }

      $htmlScheduleMain = '';


      $titleBlock = array();

      foreach($headerBlock->find('div[class*="p-schedule_match-title"]') as $tb) {
      $titleBlock[] = $tb;
      }

      $titleStage = array(); // "Закрытая квалификация"

      foreach($titleBlock[0]->find('h2') as $t) {
      $title[] = $t->innertext;
      }

      $titleDate = array(); // "суббота, 26 января, 2019"
      $titleDateFormat = array(); // "2019-01-26"
      $dateTextFormat = array(); // Array ( [text] => воскресенье, 27 января, 2019 [format] => 2019-01-27 )

      foreach($contentBlock->find('p[class*="p-schedule_item_day-title"]') as $td) {
       $titleDate[] = $td->plaintext;
       $titleDateFormat[] = $td->attr['data-date'];
      }

      for($i = 0; $i < count($titleDate); $i++) {
       $dateTextFormat[$i]['text'] = $titleDate[$i];
       $dateTextFormat[$i]['format'] = $titleDateFormat[$i];
      }



      $itemList = array(); // "<a.......>"
      $dateList = array(); // "2019-01-26"

      foreach($contentBlock->find('a[class*="p-schedule_item"]') as $il) {
      $itemList[] = $il;
      $dateList[] = $il->attr['data-date'];
      }


      $matchList = array();

      /*

      Array (
       [scheduleTime] => 16:00 ПРИБЛИЗ.
       [teamNameFull] => Array ( [0] => Future Perfect Noxus [1] => Future Perfect Zaun )
       [teamNameAcronym] => Array ( [0] => FPN [1] => FPZ )
       [scheduleResult] => Array ( [0] => Поражение [1] => Выигрыш )
       [scheduleBestOf] => Array ( [0] => До 2 побед )
       [score] => Array ( [0] => 0-2 )
      )

      */

      for ($i = 0; $i < count($itemList); $i++) {
       foreach($itemList[$i]->find('a[class*="p-schedule_item"]') as $ml) {
        $matchList[$i]['scheduleDate'] = $ml->attr['data-date'];
       }
       foreach($itemList[$i]->find('div[class="p-schedule_time"]') as $ml) {
        $matchList[$i]['scheduleTime'] = $ml->innertext;
       }
       foreach($itemList[$i]->find('span[class="p-schedule_team-name--full"]') as $ml) {
        $matchList[$i]['teamNameFull'][] = $ml->innertext;
       }
       foreach($itemList[$i]->find('span[class="p-schedule_team-name--acronym"]') as $ml) {
        $matchList[$i]['teamNameAcronym'][] = $ml->innertext;
       }
       foreach($itemList[$i]->find('p[class*="p-schedule_result"]') as $ml) {
        $matchList[$i]['scheduleResult'][] = $ml->innertext;
       }
       foreach($itemList[$i]->find('div[class="p-schedule_best-of"]') as $ml) {
        $matchList[$i]['scheduleBestOf'][] = $ml->innertext;
       }
       foreach($itemList[$i]->find('span[class="score"]') as $ml) {
        $matchList[$i]['score'][] = $ml->innertext;
       }
      }

      for($i = 0; $i < count($matchList); $i++) {
       $matchList[$i]['scheduleDateFormat'] = $dateList[$i];
      }

      /* 0 - left 1 - right */

      $answer = '';



      for ($i = 0; $i < count($dateTextFormat); $i++) {
       $answer .= '📅 '.$dateTextFormat[$i]['text']."\n";
       for($j = 0; $j < count($matchList); $j++) {
         if($dateTextFormat[$i]['format'] == $matchList[$j]['scheduleDateFormat']) {
           $pattern = '/[0-9][0-9]:[0-9][0-9]/';
           preg_match($pattern, $matchList[$j]['scheduleTime'], $timeMatches);
           if ((!isset($matchList[$j]['teamNameAcronym'][0])) && (!isset($matchList[$j]['teamNameAcronym'][1]))) {
             $answer .=  $timeMatches[0].' : '.'Тай-брейки'."\n";
           } else {
             $answer .=  $timeMatches[0].' : '.$matchList[$j]['teamNameAcronym'][0].' '.$matchList[$j]['score'][0].' '.$matchList[$j]['teamNameAcronym'][1]."\n";
           }
         }
       }
      }

      $answer = $answer."\nИсточник: $linkScheduleMain";
    $bot->sendMessage($message->getChat()->getId(),$answer);
    });
    $bot->run();
} catch (\TelegramBot\Api\Exception $e) {
    $e->getMessage();
}
