<?php

require_once "Comments.php";
require_once "DatabaseControl.php";

class CommentsStatistics extends Comments {
    
    private function getNumberOfCommentsFromDB(){
        $query = "SELECT COUNT(*) AS numberOfComments FROM $this->tableName";
        
        if (@!($fetched = $this->performQuery($query, true)))
            throw new Exception("Couldn't coun't comments in database!");

        return (int) $fetched['numberOfComments'];
    }
    
    public function countAll(int $startingDate = null, int $endingDate = null): int{ // returns -1 if error occurs
        try {
            if ($startingDate !== null || $endingDate !== null)
                $number = $this->countDuringPeroid($startingDate, $endingDate);
            
            else 
                $number = $this->getNumberOfCommentsFromDB();
            
            return (is_int($number)) ? $number : -1;
        } catch (Exception $e){
            $this->reportException($e);
            return -1;
        }
    }
    
    private function countForArticleFromDB(string $articleUrl){
        $articleUrl = $this->sanitizeInput($articleUrl);
        
        $query = "SELECT COUNT(*) AS numberOfComments FROM $this->tableName WHERE ArticleUrl = '$articleUrl'";
      
        if (@!($rowsNumber = $this->performQuery($query, true))) 
            throw new Exception("Couldn't count number of comments in the article with id = $articleUrl!");
        
        return (int)$rowsNumber[numberOfComments];
    }
    
    public function countForArticle(string $articleUrl): int{ // returns -1 if error occurs
        try {
            $number = $this->countForArticleFromDB($this->sanitizeInput($articleUrl));
            return (is_int($number)) ? $number : -1;
        } catch (Exception $e){
            $this->reportException($e);
            return -1;
        }
    }
    
    public function renderPanel(string $destination, $startingDate = null, $endingDate = null, $score = 0){
        
        if ($score === null) $score = 0;
        
        $lastWeek = time()-(60*60*24*7);
        $lastMonth = time()-(60*60*24*30);
        $lastYear = time()-(60*60*24*365);
        $fromBeginning = 0;
        
        echo<<<END
            <div class="statisticsPanel"> 
                <header class="statisticsPanelHeader">Statystyki komentarzy</header>
                <form action="$destination" id="statisticsForm" method="POST">
                <label for="startingDatePicker">Okres wyświetlania statystyk</label>
                <select id="startingDatePicker" name="startingDatePicker" onselect="submitStatsSelection()">
                    <option selected>-- Wybierz okres --</option>
                    <option value="$lastWeek">Ostatni tydzień</option>
                    <option value="$lastMonth">Ostatnie 30 dni</option>
                    <option value="$lastYear">Ostatni rok</option>
                    <option value="$fromBeginning">Od początku</option>
                </select>
                </form>
                <p>Komentarzy na stronie: $score</p>
            </div>
            
            <script>
                function submitStatsSelection(){
                     document.getElementById("statisticsForm").submit();
                }
                
                document.getElementById("startingDatePicker").addEventListener("change", submitStatsSelection); 
                
            </script>
END;
    }
    
    private function convertTimestampToDate(int $timestamp): string{
        return date("Y-m-d G:i:s", $timestamp);
    }
    
    private function handleTimePeriod(bool $isEnding, int $timestamp = null){
        if ($isEnding)
            return ($timestamp === null) ? date("Y-m-d G:i:s") : $this->convertTimestampToDate($timestamp);
        else
            return ($timestamp === null) ? date("00-00-00 00:00:00") : $this->convertTimestampToDate($timestamp);
    }
    
    private function countDuringPeroid($startingDate, $endingDate){
        $startingDate = $this->handleTimePeriod(false, $startingDate);
        $endingDate = $this->handleTimePeriod(true, $endingDate);
        
        $query = "SELECT COUNT(*) AS numberOfComments FROM $this->tableName WHERE additionDate BETWEEN '$startingDate' AND '$endingDate'";
        
        if (@!($fetched = $this->performQuery($query, true)))
            throw new Exception("Couldn't coun't comments in database!");

        return (int) $fetched['numberOfComments'];
    }
    
    
    
}