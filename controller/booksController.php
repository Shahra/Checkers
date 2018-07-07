<?php 

class BooksController extends BaseController
{
	public function index() 
	{
		$ls = new LibraryService();

		// Popuni template potrebnim podacima
		$this->registry->template->title = 'Popis svih knjiga knjižnice';
		$this->registry->template->bookList = $ls->getAllBooks();

        $this->registry->template->show( 'books_index' );
	}

	public function search() 
	{
		$this->registry->template->title = 'Pretraži knjige po autoru';
		$this->registry->template->show( 'books_search' );
	}

	public function searchResults() 
	{
		$ls = new LibraryService();


		if( !isset( $_POST['author'] ) || !preg_match( '/^[a-zA-Z ,-.]+$/', $_POST['author'] ) )
		{
			header( 'Location: ' . __SITE_URL . '/index.php?rt=books/search');
			exit();
		}

		$this->registry->template->title = 'Popis svih knjiga autora ' . $_POST['author'];
		$this->registry->template->bookList = $ls->getBooksByAuthorName( $_POST['author'] );

		$this->registry->template->show( 'books_index' );
	}	
}; 

?>
