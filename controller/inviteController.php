<?php

class inviteController extends BaseController
{
	public function index() {}

    public function pozoviNaIgru() {

		$is = new InviteService();
		$name = $is->napravi_igru($_POST['username_igraca_kojeg_zovemo'], $_POST['nas_username']);
		echo json_encode("osoba uspjesno pozvana");
	}

    public function obradiPoziv() {
		$is = new InviteService();
		if(isset($_POST['prihvati_poziv_usera'])) {
			$is->prihvati_poziv($_POST['prihvati_poziv_usera'], $_POST['moj_username']);
			echo json_encode(['poziv' => 'odbijen']);
		} else if(isset($_POST['odbijen_user'])) {
			$is->odbij_poziv($_POST['odbijen_user'], $_POST['moj_username']);
			echo json_encode(['poziv' => 'odbijen']);
		} else {
			$game = $is->imam_li_poziv($_POST['username']);
			if($game['username_bijelog']) {
				echo json_encode($game);
			} else {
				echo json_encode(['poziv' => 'nema_poziva']);
			}
		}
	}
};

?>
