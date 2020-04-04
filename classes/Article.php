<?php

require_once "DatabaseControl.php";
require_once "AddingArticle.php";

class Article{ 
    use DatabaseControl;
    
    protected $id;
    protected $title;
    protected $content;
    protected $photo;
    protected $articleUrl;
    protected $publicationDate;
    protected $category;
    protected $additionalCategory;
    
    
    protected function sanitizeInput(string $input): string{
        return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    
    protected function setUrl(string $url): void{
        $this->articleUrl = $this->sanitizeInput($url);
    }
    
    protected function loadByUrl(): bool{
        if (empty($this->articleUrl)) return false;
        if (!$this->loadFromDBUsingUrl()) return false;
        return true;
    }
    
    
    public function __construct(string $articleUrl){
        $this->setUrl($articleUrl);
        $this->loadByUrl();
    }
    
    protected function loadFromDBUsingUrl(): bool{
        $table = Article::$contentTable;
        
        $query = "SELECT * FROM $table WHERE articleUrl = '$this->articleUrl'";
        if (!($data = $this->performQuery($query, true))) return false;
        
        $this->id = $data['news_id'];
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->photo = $data['photo'];
        $this->publicationDate = $data['publicationDate'];
        $this->category = $data['category'];
        $this->additionalCategory = $data['additionalCategory'];
        
        return true;
    }
    
    public function renderArticle(){
        $photoDir = AddingArticle::$photoDirectory;
        echo '<article class="article"><h1 class="articleTitle">'.$this->title.'</h1>';
        echo '<img src="'.$photoDir.'/'.$this->photo.'" class="mainArticleImage" alt="Zdjęcie dla artykuły pt. '.$this->title.'">';
        echo '<div class="articleText">'.$this->content.'</div>';
        echo '</article>';
    }
    
    
}