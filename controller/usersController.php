<?php 

class UsersController extends BaseController
{
	public function index() 
	{
		$ls = new LibraryService();

		// Popuni template potrebnim podacima
		$this->registry->template->title = 'Popis svih korisnika knjižnice';
		$this->registry->template->userList = $ls->getAllUsers();

        $this->registry->template->show( 'users_index' );
	}
}; 

?>
