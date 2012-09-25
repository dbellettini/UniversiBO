<?php
namespace Universibo\Bundle\LegacyBundle\Command;
use \Error;

use Universibo\Bundle\LegacyBundle\Entity\Canale;
use Universibo\Bundle\LegacyBundle\Entity\Commenti\CommentoItem;
use Universibo\Bundle\LegacyBundle\Entity\Files\FileItemStudenti;
use Universibo\Bundle\LegacyBundle\App\UniversiboCommand;

/**
 * FileStudentiCommentDelete: Cancella un commento di un File Studente
 *
 * @package universibo
 * @subpackage commands
 * @version 2.0.0
 * @author Ilias Bartolini <brain79@virgilio.it>
 * @author Fabrizio Pinto
 * @author Daniele Tiles
 * @license GPL, {@link http://www.opensource.org/licenses/gpl-license.php}
 */

class FileStudentiCommentDelete extends UniversiboCommand
{
    public function execute()
    {

        $frontcontroller = $this->getFrontController();
        $template = $frontcontroller->getTemplateEngine();

        $krono = $frontcontroller->getKrono();

        $user = $this->get('security.context')->getToken()->getUser();
        $user_ruoli = $user instanceof User ? $this->get('universibo_legacy.repository.ruolo')->findByIdUtente($user->getId()) : array();

        if (!array_key_exists('id_commento', $_GET)
                || !preg_match('/^([0-9]{1,9})$/', $_GET['id_commento'])) {
            Error::throwError(_ERROR_DEFAULT,
                    array('id_utente' => $user->getIdUser(),
                            'msg' => 'L\'id del commento non e` valido',
                            'file' => __FILE__, 'line' => __LINE__));
        }

        $id_commento = $_GET['id_commento'];
        $commento = CommentoItem::selectCommentoItem($id_commento);
        $id_utente = $commento->getIdUtente();
        $id_file_studente = $commento->getIdFileStudente();

        $template
                ->assign('common_canaleURI',
                        array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER']
                                : '');
        $template->assign('common_langCanaleNome', 'indietro');

        $referente = false;
        $moderatore = false;

        $autore = ($id_utente == $user->getIdUser());

        if (array_key_exists('id_canale', $_GET)) {
            if (!preg_match('/^([0-9]{1,9})$/', $_GET['id_canale']))
                Error::throwError(_ERROR_DEFAULT,
                        array('id_utente' => $user->getIdUser(),
                                'msg' => 'L\'id del canale richiesto non e` valido',
                                'file' => __FILE__, 'line' => __LINE__));

            $canale = Canale::retrieveCanale($_GET['id_canale']);
            $id_canale = $_GET['id_canale'];
            if ($canale->getServizioFilesStudenti() == false)
                Error::throwError(_ERROR_DEFAULT,
                        array('id_utente' => $user->getIdUser(),
                                'msg' => "Il servizio files studenti e` disattivato",
                                'file' => __FILE__, 'line' => __LINE__));

            if (array_key_exists($id_canale, $user_ruoli)) {
                $ruolo = $user_ruoli[$id_canale];

                $referente = $ruolo->isReferente();
                $moderatore = $ruolo->isModeratore();
            }
            //controllo coerenza parametri
            $file = FileItemStudenti::selectFileItem($id_file_studente);
            $canali_file = $file->getIdCanali();

            //TODO: perché non funziona il controllo???

            //			var_dump($canali_file);
            //			die();
            //			if (!in_array($id_canale, $canali_file))
            //				 Error :: throwError(_ERROR_DEFAULT, array ('id_utente' => $user->getIdUser(), 'msg' => 'I parametri passati non sono coerenti', 'file' => __FILE__, 'line' => __LINE__));

            $elenco_canali = array($id_canale);

            //controllo diritti sul canale
            if (!($this->get('security.context')->isGranted('ROLE_ADMIN') || $referente || $moderatore || $autore))
                Error::throwError(_ERROR_DEFAULT,
                        array('id_utente' => $user->getIdUser(),
                                'msg' => "Non hai i diritti per eliminare il commento\n La sessione potrebbe essere scaduta",
                                'file' => __FILE__, 'line' => __LINE__));

        } elseif (!($this->get('security.context')->isGranted('ROLE_ADMIN') || $autore))
            Error::throwError(_ERROR_DEFAULT,
                    array('id_utente' => $user->getIdUser(),
                            'msg' => "Non hai i diritti per eliminare il commento\n La sessione potrebbe essere scaduta",
                            'file' => __FILE__, 'line' => __LINE__));

        // valori default form
        // $f28_file = '';

        $this
                ->executePlugin('ShowFileStudentiCommento',
                        array('id_commento' => $id_commento));

        $f28_accept = false;

        if (array_key_exists('f28_submit', $_POST)) {
            $f28_accept = true;

            //esecuzione operazioni accettazione del form
            if ($f28_accept == true) {

                CommentoItem::deleteCommentoItem($id_commento);
                $template
                        ->assign('common_canaleURI',
                                '/?do=FileShowInfo&id_file='
                                        . $id_file_studente . '&id_canale='
                                        . $id_canale);

                return 'success';
            }

        }
        //end if (array_key_exists('f28_submit', $_POST))

        // resta da sistemare qui sotto, fare il form e fare debugging
        return 'default';

    }

}
