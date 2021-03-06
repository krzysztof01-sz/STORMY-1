<?php

require_once "DatabaseControl.php";
require_once "AddingArticle.php";
require_once "Categories.php";

interface iArticle {
    public function getTitle(): ?string;
    public function getAuthor(): ?string;
    public function renderArticle(): void;
}

class Article implements iArticle{ 
    use DatabaseControl;
    
    protected $id;
    protected $title;
    protected $content;
    protected $photo;
    protected $articleUrl;
    protected $author;
    protected $publicationDate;
    protected $category;
    protected $additionalCategory;
    
    public function getTitle(): ?string{
        return $this->title;
    }
    
    public function getAuthor(): ?string{
        return $this->author;
    }
    
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
        if ($this->loadByUrl() === false) // if a page doesn't exist, a user is being redirected to the home page
            header('location: index.php');
    }
    
    protected function loadFromDBUsingUrl(): bool{
        $table = Article::$contentTable;
        
        $query = "SELECT * FROM $table WHERE articleUrl = '$this->articleUrl'";
        if (!($data = $this->performQuery($query, true))) return false;
        
        $this->id = $data['news_id'];
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->author = $data['author'];
        $this->photo = $data['photo'];
        $this->publicationDate = $data['publicationDate'];
        $this->category = $data['category'];
        $this->additionalCategory = $data['additionalCategory'];
        
        return true;
    }
    
    public function renderArticle(): void{
        $categories = new Categories;
        $category = $categories->getCategoryName($this->category);
        $photoDir = AddingArticle::$photoDirectory;
        $publicationDate = substr($this->publicationDate, 0, 16);
        
        echo '<article class="article"><h1 class="articleTitle">'.$this->title.'</h1>';
        echo '<div class="articleInfo">Opublikowano: '.$publicationDate.' | Kategoria: '.$category.' | Autor: '.$this->author.'</div>';
        echo '<img src="'.$photoDir.$this->photo.'" class="mainArticleImage" alt="Zdjęcie dla artykuły pt. '.$this->title.'">';
        echo '<div class="articleText">'.$this->content.'</div>';
        echo '</article>';
    }
    
    
}