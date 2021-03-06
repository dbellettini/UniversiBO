<?php
namespace Universibo\Bundle\LegacyBundle\Command;

use Symfony\Component\HttpFoundation\Request;
use Universibo\Bundle\LegacyBundle\App\CanaleCommand;
use Universibo\Bundle\LegacyBundle\Entity\Canale;

/**
 * ShowCanale: mostra un canale
 *
 * @package universibo
 * @subpackage commands
 * @version 2.0.0
 * @author Fabrizio Pinto
 * @license GPL, {@link http://www.opensource.org/licenses/gpl-license.php}
 */
class ShowCanale extends CanaleCommand
{
    public function execute(Request $request)
    {
        $check = $this->ensureChannelType(Canale::CDEFAULT);
        if ($check !== null) {
            return $check;
        }

        $frontcontroller = $this->getFrontController();
        $template = $frontcontroller->getTemplateEngine();

        $canale = $this->getRequestCanale();

        // controllare se è settato il nome e l'immagine?
        $template->assign('showCanale_titolo', $canale->getNome());
        $template->assign('showCanale_img', $canale->getImmagine());
        //var_dump($canale->getImmagine());

        $template->assign('showCanale_filesFlag', 'false');
        $template->assign('showCanale_newsFlag', 'false');

        //var_dump($canale->getServizioNews());
        if ($canale->getServizioNews()) {
            $template->assign('showCanale_newsFlag', 'true');
            $this->executePlugin('ShowNewsLatest', [ 'num' => 5  ]);
        }

        //var_dump($canale->getServizioFiles());
        if ($canale->getServizioFiles()) {
            $template->assign('showCanale_filesFlag', 'true');
            $this->executePlugin('ShowFileTitoli', []);
        }

        if ($canale->getServizioLinks()) {
            $template->assign('showCanale_linksFlag', 'true');
            $this->executePlugin('ShowLinks', [ 'num' => 12 ] );
        }

        return 'default';
    }
}
