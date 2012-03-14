<?php
namespace UniversiBO\Legacy\App;

require_once('Ruolo'.PHP_EXTENSION);

define('CANALE_DEFAULT'      ,1);
define('CANALE_HOME'         ,2);
define('CANALE_FACOLTA'      ,3);
define('CANALE_CDL'          ,4);
define('CANALE_INSEGNAMENTO' ,5);
//define('CANALE_ESAME_ECO'    ,6);

use UniversiBO\Legacy\Framework\FrontController;
use \Error;
use \DB;

/**
 * Canale class.
 *
 * Un "canale" ? una pagina dinamica con a disposizione il collegamento 
 * verso i vari servizi tramite un indentificativo, gestisce i diritti di
 * accesso per i diversi gruppi e diritti particolari 'ruoli' per alcuni utenti,
 * fornisce sistemi di notifica e per assegnare un nome ad un canale
 *
 * @package universibo
 * @version 2.0.0
 * @author Ilias Bartolini <brain79@virgilio.it>
 * @license GPL, @link http://www.opensource.org/licenses/gpl-license.php
 * @copyright CopyLeft UniversiBO 2001-2003
 */

class Canale {
	
	/**
	 * @private
	 */
	var $id_canale = 0;
	/**
	 * @private
	 */
	var $permessi = 0;  
	/**
	 * @private
	 */
	var $ultimaModifica = 0;
	/**
	 * @private
	 */
	var $tipoCanale = 0;
	/**
	 * @private
	 */
	var $immagine = '';
	/**
	 * @private
	 */
	var $nome = '';
	/**
	 * @private
	 */
	var $visite = 0;
	/**
	 * @private
	 */
	var $servizioNews = false;
	/**
	 * @private
	 */
	var $servizioFiles = false;
	/**
	 * @private
	 */
	var $servizioForum = false;
	/**
	 * @private
	 */
	var $forum = array();
	/**
	 * @private
	 */
	var $servizioLinks = false;
	/**
	 * @private
	 */
	var $ruoli = NULL;
	/**
	 * @private
	 */
	var $servizioFilesStudenti = false;
	
	
	
	/**
	 * Crea un oggetto canale 
	 *
	 * $tipo_canale:
	 *  define('CANALE_DEFAULT'      ,1);
	 *  define('CANALE_HOME'         ,2);
	 *  define('CANALE_FACOLTA'      ,3);
	 *  define('CANALE_CDL'          ,4);
	 *  define('CANALE_INSEGNAMENTO' ,5);
	 *
	 * @see factoryCanale
	 * @see selectCanale
	 * @param int $id_canale 		identificativo del canae su database
	 * @param int $permessi 		privilegi di accesso gruppi {@see User}
	 * @param int $ultima_modifica 	timestamp 
	 * @param int $tipo_canale 	 	vedi definizione dei tipi sopra
	 * @param string  $immagine		uri dell'immagine relativo alla cartella del template
	 * @param string $nome			nome del canale
	 * @param int $visite			numero visite effettuate sul canale
	 * @param boolean $news_attivo	se true il servizio notizie ? attivo
	 * @param boolean $files_attivo	se true il servizio false ? attivo
	 * @param boolean $forum_attivo	se true il servizio forum ? attivo
	 * @param int $forum_forum_id	se forum_attivo ? true indica l'identificativo del forum su database
	 * @param int $forum_group_id	se forum_attivo ? true indica l'identificativo del grupop moderatori del forum su database
	 * @param boolean $links_attivo se true il servizio links ? attivo
	 * @return Canale
	 */
	public function __construct($id_canale, $permessi, $ultima_modifica, $tipo_canale, $immagine, $nome, $visite,
				 $news_attivo, $files_attivo, $forum_attivo, $forum_forum_id, $forum_group_id, $links_attivo,$files_studenti_attivo)
	{
		$this->id_canale = $id_canale;
		$this->permessi = $permessi;
		$this->ultimaModifica = $ultima_modifica;
		$this->tipoCanale = $tipo_canale;
		$this->immagine = $immagine;
		$this->nome = $nome;
		$this->visite = $visite;
		$this->servizioNews = $news_attivo;
		$this->servizioFiles = $files_attivo;
		$this->servizioForum = $forum_attivo;
		$this->forum['forum_id'] = $forum_forum_id;
		$this->forum['group_id'] = $forum_group_id;
		$this->servizioLinks = $links_attivo;
		$this->servizioFilesStudenti = $files_studenti_attivo;
		$this->bookmark = NULL;
	}



	/**
	 * Ritorna l'id_canale che identifica il canale
	 *
	 * @return int
	 */
	function getIdCanale()
	{
		return $this->id_canale;
	}



	/**
	 * Ritorna l' OR bit a bit dei gruppi che hanno accesso al canale
	 *
	 * @return int
	 */
	function getPermessi()
	{
		return $this->permessi;
	}
	
	
	function setPermessi($permessi)
	{
		return $this->permessi = $permessi;
	}

	
	/**
	 * Restituisce true se il gruppo o uno dei gruppi appartenenti a $groups 
	 * ha il permesso di accesso al canale, altrimenti false
	 *
	 * @param int $groups gruppi di cui si vuole verificare l'accesso
	 * @return boolean
	 */
	function isGroupAllowed($groups)
	{
		return (boolean) ((int)$this->permessi & (int)$groups);
	}
	
	
	
	/**
	 * Ritorna il tipo di canale
	 *
	 * es: $tipo_canale:
	 *  define('CANALE_DEFAULT'   ,1);
	 *  define('CANALE_HOME'      ,2);
	 *  define('CANALE_FACOLTA'   ,3);
	 *  define('CANALE_CDL'       ,4);
	 *  define('CANALE_INSEGNAMENTO' ,5);
	 * 
	 * @static
	 * @param int $id_canale numero identificativo del canale
	 * @return int intero (tipo_canale) se eseguita con successo, false se il canale non esiste
	 */
	function getTipoCanaleFromId($id_canale)
	{
		$db = \FrontController::getDbConnection('main');
	
		$query = 'SELECT tipo_canale FROM canale WHERE id_canale= '.$db->quote($id_canale);
		$res = $db->query($query);
		if (\DB::isError($res)) 
			\Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
	
		$rows = $res->numRows();
		if( $rows > 1) \Error::throwError(_ERROR_CRITICAL,array('msg'=>'Errore generale database: canale non unico','file'=>__FILE__,'line'=>__LINE__));
		if( $rows = 0) return false;

		$res->fetchInto($row);
		return $row[0];
	}
	
	
	
	/**
	 * Ritorna il tipo di canale dato l'id_canale del database
	 *
	 * @return int
	 */
	function getTipoCanale()
	{
		return $this->tipoCanale;
	}
	
	
	
	/**
	 * Ritorna il timestamp dell'ultima modifica eseguita nel canale
	 *
	 * @return int
	 */
	function getUltimaModifica()
	{
		return $this->ultimaModifica;
	}
	
	
	/**
	 * Imposta il timestamp dell'ultima modifica
	 *
	 * @todo implementare propagazione DB
	 * @param boolean $attiva_files 
	 * @param boolean $updateDB se true la modifica viene propagata al DB 
	 * @return boolean
	 */
	function setUltimaModifica($timestamp, $updateDB = false)
	{
		$this->ultimaModifica = $timestamp;
		if ( $updateDB == true )
		{
			$db = \FrontController::getDbConnection('main');
		
			$query = 'UPDATE canale SET ultima_modifica = '.$db->quote($timestamp).' WHERE id_canale = '.$db->quote($this->getIdCanale());
			$res = $db->query($query);
			if (\DB::isError($res)) 
				\Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
			$rows = $db->affectedRows();
		
			if( $rows == 1) return true;
			elseif( $rows == 0) return false;
			else \Error::throwError(_ERROR_CRITICAL,array('msg'=>'Errore generale database canali: id non unico','file'=>__FILE__,'line'=>__LINE__));
			return false;
		}
		return true;
		
	}


	/**
	 * Ritorna l'URL relativo alla cartella del template dell'immagine di intestazione del canale
	 *
	 * @return string
	 */
	function getImmagine()
	{
		return $this->immagine;
	}



	/**
	 * Ritorna la stringa descrittiva del titolo/nome breve del canale
	 *
	 * @return string
	 */
	function getNome()
	{
		return $this->nome;
	}

	function getNomeCanale()
	{
		return $this->nome;
	}

	/**
	 * Ritorna la stringa descrittiva del titolo/nome breve del canale per il MyUniversiBO
	 *
	 * @return string
	 */
	function getNomeMyUniversiBO()
	{
		return $this->getNome();
	}

	/**
	 * Restituisce se la stringa descrittiva nome ? impostata
	 *
	 * @return string
	 */
	function isNomeSet()
	{
		return $this->nome!='' && $this->nome!=NULL;
	}



	/**
	 * Ritorna la stringa descrittiva del titolo/nome completo del canale
	 *
	 * @return string
	 */
	function getTitolo()
	{
		return $this->getNome();
	}



	/**
	 * Ritorna il numero di visite effettuate nel canale
	 *
	 * @return int
	 */
	function getVisite()
	{
		return $this->visite;
	}



	/**
	 * Aumenta il numero di visite effettuate nel canale
	 *
	 * @return boolean
	 */
	function addVisite($visite = 1)
	{
		$this->visite += $visite;

		$db = \FrontController::getDbConnection('main');
	
		$query = 'UPDATE canale SET visite = visite + '.$db->quote($visite).' WHERE id_canale = '.$db->quote($this->getIdCanale());
		$res = $db->query($query);
		if (\DB::isError($res)) 
			\Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
		$rows = $db->affectedRows();
	
		if( $rows == 1) return true;
		elseif( $rows == 0) return false;
		else \Error::throwError(_ERROR_CRITICAL,array('msg'=>'Errore generale database: canale non unico','file'=>__FILE__,'line'=>__LINE__));

	}



	/**
	 * Ritorna l'oggetto News, false se il servizio non ? attivo
	 *
	 * @todo implementare News
	 * @return mixed
	 */
	function getServizioNews()
	{
		return $this->servizioNews;
	}



	/**
	 * Imposta il servizio news, true: attivo - false: non attivo
	 *
	 * @todo implementare propagazione DB
	 * @param boolean $attiva_files 
	 * @param boolean $updateDB se true la modifica viene propagata al DB 
	 * @return boolean
	 */
	function setServizioNews($attiva_news, $updateDB = false)
	{
		$this->servizioNews = $attiva_news;
	}



	/**
	 * Ritorna l'oggetto Files, false se il servizio non ? attivo
	 *
	 * @todo implementare Files
	 * @return mixed
	 */
	function getServizioFiles()
	{
		return $this->servizioFiles;
	}



	/**
	 * Imposta il servizio files, true: attivo - false: non attivo
	 *
	 * @todo implementare propagazione DB
	 * @param boolean $attiva_files 
	 * @param boolean $updateDB se true la modifica viene propagata al DB 
	 * @return boolean
	 */
	function setServizioFiles($attiva_files, $updateDB = false)
	{
		$this->servizioFiles = $attiva_files;
	}



	/**
	 * Ritorna l'oggetto Links, false se il servizio non ? attivo
	 *
	 * @todo implementare Links
	 * @return mixed
	 */
	function getServizioLinks()
	{
		return $this->servizioLinks;
	}



	/**
	 * Imposta il servizio links, true: attivo - false: non attivo
	 *
	 * @todo implementare propagazione DB
	 * @param boolean $attiva_links 
	 * @param boolean $updateDB se true la modifica viene propagata al DB 
	 * @return boolean
	 */
	function setServizioLinks($attiva_links, $updateDB = false)
	{
		$this->servizioLinks = $attiva_links;
	}
	
	/**
	 * Ritorna true , false se il servizio non ? attivo
	 *
	 * @todo implementare Files
	 * @return boolean
	 */
	function getServizioFilesStudenti()
	{
		return $this->servizioFilesStudenti;
	}



	/**
	 * Imposta il servizio Files Studenti, true: attivo - false: non attivo
	 *
	 * @todo implementare propagazione DB
	 * @param boolean $attiva_files 
	 * @param boolean $updateDB se true la modifica viene propagata al DB 
	 * @return boolean
	 */
	function setServizioFilesStudenti($attiva_files_studenti, $updateDB = false)
	{
		$this->servizioFilesStudenti = $attiva_files_studenti;
	}



	/**
	 * Ritorna l'oggetto Forum, false se il servizio non ? attivo
	 *
	 * @todo implementare Forum
	 * @return mixed
	 */
	function getServizioForum()
	{
		return $this->servizioForum;
	}



	/**
	 * Imposta il servizio forum, true: attivo - false: non attivo
	 *
	 * @todo implementare propagazione DB
	 * @param boolean $attiva_links 
	 * @param boolean $updateDB se true la modifica viene propagata al DB 
	 * @return boolean
	 */
	function setServizioForum($attiva_forum, $updateDB = false)
	{
		$this->servizioForum = $attiva_forum;
	}



	/**
	 * Ritorna il forum_id delle tabelle di phpbb, , NULL se il forum non ? attivo
	 *
	 * @return mixed
	 */
	function getForumForumId()
	{
		return $this->forum['forum_id'];
	}



	/**
	 * Ritorna il group_id delle tabelle di phpbb, NULL se il forum non ? attivo
	 *
	 * @return mixed
	 */
	function getForumGroupId()
	{
		return $this->forum['group_id'];
	}


	
	/**
	 * Imposta il forum_id delle tabelle di phpbb, , NULL se il forum non ? attivo
	 */
	function setForumForumId($forum_id)
	{
		$this->forum['forum_id'] = $forum_id;
	}



	/**
	 * Imposta il group_id delle tabelle di phpbb, NULL se il forum non ? attivo
	 */
	function setForumGroupId($group_id)
	{
		$this->forum['group_id'] = $group_id;
	}



	/**
	 * Inizializza le informazioni del canale di CanaleCommand 
	 * Esegue il dispatch inizializzndo il corretto sottotipo di 'canale' 
	 *
	 * @private
	 * @return string
	 */
	function _dispatchCanale()
	{

		//$tipo_canale =  Canale::getTipoCanaleFromId ( $this->getRequestIdCanale() );

		$this->requestCanale = Canale::selectCanale( $this->getRequestIdCanale() );
			  
		if ( $this->requestCanale === false ) 
			\Error::throwError(_ERROR_DEFAULT,array('msg'=>'Il canale richiesto non � presente','file'=>__FILE__,'line'=>__LINE__));
		
		$canale = $this->getRequestCanale();
		$canale->addVisite();
	}
	
	
	
	/**
	 * Crea un oggetto "figlio di Canale" dato il suo numero identificativo id_canale
	 * Questo metodo utilizza il metodo factory che viene ridefinito nelle 
	 * sottoclassi per restituire un oggetto del tipo corrispondente
	 *
	 * @static
	 * @param int $id_canale numero identificativo del canale
	 * @return mixed Canale se eseguita con successo, false se il canale non esiste
	 */
	function retrieveCanale($id_canale, $cache = true)
	{
		//spalata la cache!!! 
		//dimezza i tempi di esecuzione!!
		static $cache_canali = array();
		
		if ($cache == true && array_key_exists($id_canale, $cache_canali))
			return $cache_canali[$id_canale];
		
		$tipo_canale =  Canale::getTipoCanaleFromId ( $id_canale );
		if ($tipo_canale === false )
			Error::throwError(_ERROR_DEFAULT,array('msg'=>'Il canale richiesto non � presente','file'=>__FILE__,'line'=>__LINE__));
		
		$dispatch_array = array (	CANALE_DEFAULT      => 'Canale',
									CANALE_HOME         => 'Canale',
									CANALE_FACOLTA      => 'Facolta',
									CANALE_CDL          => 'Cdl',
									CANALE_INSEGNAMENTO => 'Insegnamento');
		
		
		if (!array_key_exists($tipo_canale, $dispatch_array))
		{
			Error::throwError(_ERROR_CRITICAL,array('msg'=>'Il tipo di canale richiesto su database non � valido, contattare lo staff - '.var_dump($id_canale).var_dump($tipo_canale),'file'=>__FILE__,'line'=>__LINE__));
		}
		
		$class_name = $dispatch_array[$tipo_canale];
		
		require_once($class_name.PHP_EXTENSION);
		
		$cache_canali[$id_canale] = call_user_func(array($class_name,'factoryCanale'), $id_canale);

		return $cache_canali[$id_canale];
	}
	
	
	/**
	 * Crea un oggetto Canale dato il suo numero identificativo id_canale
	 * Questo metodo viene ridefinito nelle sottoclassi per restituire un oggetto
	 * del tipo corrispondente
	 *
	 * @static
	 * @param int $id_canale numero identificativo del canale
	 * @return mixed Canale se eseguita con successo, false se il canale non esiste
	 */
	function factoryCanale($id_canale)
	{
		$canale=Canale::selectCanale($id_canale);
		return $canale;
	}
	
	
	/**
	 * Restituisce l'uri/link che mostra un canale
	 *
	 * @return string uri/link che mostra un canale
	 */
	function showMe()
	{
		if ($this->getTipoCanale() == CANALE_HOME) return 'index.php?do=ShowHome';
		else return 'index.php?do=ShowCanale&id_canale='.$this->id_canale;
	}
	
	
	/**
	 * Crea un oggetto canale dato il suo numero identificativo id_canale del database
	 *
	 * @static
	 * @param int $id_canale numero identificativo del canale
	 * @return mixed Canale se eseguita con successo, false se il canale non esiste
	 */
	function selectCanale($id_canale)
	{
		/*
		$db = \FrontController::getDbConnection('main');
	
		$query = 'SELECT tipo_canale, nome_canale, immagine, visite, ultima_modifica, permessi_groups, files_attivo, news_attivo, forum_attivo, id_forum, group_id, links_attivo FROM canale WHERE id_canale= '.$db->quote($id_canale);
		$res = $db->query($query);
		if (\DB::isError($res)) 
			Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
	
		$rows = $res->numRows();
		if( $rows > 1) Error::throwError(_ERROR_CRITICAL,array('msg'=>'Errore generale database: canale non unico','file'=>__FILE__,'line'=>__LINE__));
		if( $rows = 0) return false;

		$res->fetchInto($row);
		$canale = new Canale($id_canale, $row[5], $row[4], $row[0], $row[2], $row[1], $row[3],
						 $row[7]=='S', $row[6]=='S', $row[8]=='S', $row[9], $row[10], $row[11]=='S' );
		*/
		$array_canale = Canale::selectCanali( array( 0 => $id_canale ) );
		return $array_canale[0];
		
	}
	

	/**
	 * Crea un elenco (array) di oggetti Canale dato un elenco (array) di loro numeri identificativi id_canale del database
	 *
	 * @static
	 * @param array $elenco_id_canali array contenente i numeri identificativi del canale
	 * @return mixed array di Canale se eseguita con successo, false se il canale non esiste
	 */
	function selectCanali($elenco_id_canali)
	{
		$db = \FrontController::getDbConnection('main');
	
		array_walk($elenco_id_canali, array($db, 'quote'));
		$canali_comma = implode (' , ',$elenco_id_canali);
		
		$query = 'SELECT tipo_canale, nome_canale, immagine, visite, ultima_modifica, permessi_groups, files_attivo, news_attivo, forum_attivo, id_forum, group_id, links_attivo, id_canale, files_studenti_attivo FROM canale WHERE id_canale IN ('.$canali_comma.');';
		$res = $db->query($query);
		if (\DB::isError($res)) 
			\Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
	
		$rows = $res->numRows();
		if( $rows > count($elenco_id_canali)) Error::throwError(_ERROR_CRITICAL,array('msg'=>'Errore generale database: canale non unico','file'=>__FILE__,'line'=>__LINE__));
		if( $rows == 0) return false;

		$elenco_canali = array();
		while ($res->fetchInto($row))
		{
			//var_dump($row);
			$elenco_canali[] = new Canale($row[12], $row[5], $row[4], $row[0], $row[2], $row[1], $row[3],
						 $row[7]=='S', $row[6]=='S', $row[8]=='S', $row[9], $row[10], $row[11]=='S',$row[13]=='S' );
		}
		$res->free();
		
		return $elenco_canali;
		
	}


	/**
	 * Inserisce su Db le informazioni riguardanti un NUOVO canale
	 *
	 * @param int $id_canale numero identificativo utente
	 * @return boolean
	 */
	function insertCanale()
	{
		$db = \FrontController::getDbConnection('main');
	
		$this->id_canale = $db->nextID('canale_id_canale');
		$files_attivo = ( $this->getServizioFiles() ) ? 'S' : 'N';
		$news_attivo  = ( $this->getServizioNews()  ) ? 'S' : 'N';
		$links_attivo = ( $this->getServizioLinks() ) ? 'S' : 'N';
		$files_studenti_attivo = ( $this->getServizioFilesStudenti() ) ? 'S' : 'N';
		if ( $this->getServizioForum() )
		{
			$forum_attivo = 'S';
			$forum_forum_id = $this->getForumForumId();
			$forum_group_id = $this->getForumGroupId();
		}
		else
		{
			$forum_attivo = 'N';
			/**
			 * @todo testare se gli piace il valore NULL poi quotato nella query
			 */
			$forum_forum_id = NULL ;
			$forum_group_id = NULL ;
		}	
			
		
		$query = 'INSERT INTO canale (id_canale, tipo_canale, nome_canale, immagine, visite, ultima_modifica, permessi_groups, files_attivo, news_attivo, forum_attivo, id_forum, group_id, links_attivo, files_studenti_attivo) VALUES ('.
					$db->quote($this->getIdCanale()).' , '.
					$db->quote($this->getTipoCanale()).' , '.
					$db->quote($this->getNomeCanale()).' , '.
					$db->quote($this->getImmagine()).' , '.
					$db->quote($this->getVisite()).' , '.
					$db->quote($this->getUltimaModifica()).' , '.
					$db->quote($this->getPermessi()).' , '.
					$db->quote($files_attivo).' , '.
					$db->quote($news_attivo).' , '.
					$db->quote($forum_attivo).' , '.
					$db->quote($forum_forum_id).' , '.
					$db->quote($forum_group_id).' , '.
					$db->quote($links_attivo).' ,'.
					$db->quote($files_studenti_attivo).' )';
		$res = $db->query($query);
		if (\DB::isError($res))
		{ 
			Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__));
			return false;
		}
		
		return true;
	}
	
	
	
	/**
	 * Aggiorna il contenuto su DB riguardante le informazioni del canale
	 *
	 * @return boolean true se avvenua con successo, altrimenti false e throws Error object
	 */
	function updateCanale()
	{
		$db = \FrontController::getDbConnection('main');
		
		$files_attivo = ( $this->getServizioFiles() ) ? 'S' : 'N';
		$news_attivo  = ( $this->getServizioNews()  ) ? 'S' : 'N';
		$links_attivo = ( $this->getServizioLinks() ) ? 'S' : 'N';
		$files_studenti_attivo = ( $this->getServizioFilesStudenti() ) ? 'S' : 'N';
		if ( $this->getServizioForum() )
		{
			$forum_attivo = 'S';
			$forum_forum_id = $this->getForumForumId();
			$forum_group_id = $this->getForumGroupId();
		}
		else
		{
			$forum_attivo = 'N';
			
			$forum_forum_id = $this->getForumForumId();
			$forum_group_id = $this->getForumGroupId();
		}	
		
		
		$query = 'UPDATE canale SET tipo_canale = '.$db->quote($this->getTipoCanale()).
					' , nome_canale = '.$db->quote($this->nome).   //<--attento
					' , immagine = '.$db->quote($this->getImmagine()).
					' , ultima_modifica = '.$db->quote($this->getUltimaModifica()).
					' , permessi_groups = '.$db->quote($this->getPermessi()).
					' , files_attivo = '.$db->quote($files_attivo).
					' , news_attivo = '.$db->quote($news_attivo).
					' , forum_attivo = '.$db->quote($forum_attivo).
					' , id_forum = '.$db->quote($forum_forum_id).
					' , group_id = '.$db->quote($forum_group_id).
					' , links_attivo = '.$db->quote($links_attivo).
					' , files_studenti_attivo = '.$db->quote($files_studenti_attivo).' WHERE id_canale ='.$db->quote($this->getIdCanale());
			
		$res = $db->query($query);
		if (\DB::isError($res)) 
			\Error::throwError(_ERROR_CRITICAL,array('msg'=>DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
		$rows = $db->affectedRows();
		
		if( $rows == 1) return true;
		elseif( $rows == 0) return false;
		else \Error::throwError(_ERROR_CRITICAL,array('msg'=>'Errore generale database: canale non unico','file'=>__FILE__,'line'=>__LINE__));
	}
	
	
	/**
	 * Ritorna un array contenente gli oggetti Ruolo associati al canale
	 *
	 * @return array
	 */
	function getRuoli()
	{
		if ($this->ruoli == NULL)
		{
			$this->ruoli = array();
			$ruoli = \Ruolo::selectCanaleRuoli($this->getIdCanale());
			$num_elementi = count($ruoli);
			for ($i=0; $i<$num_elementi; $i++)
			{
				$this->ruoli[$ruoli[$i]->getIdUser()] = $ruoli[$i];
			}
		}
		//var_dump($this->ruoli);
		return $this->ruoli;
	}
	
	/**
	 * Controlla se esiste un canale
	 *
	 * @static
	 *
	 * @param int $id_canale	id del canale da controllare
	 * @return boolean true se esiste tale canale
	 */
	function canaleExists($id_canale){
		
		if ( $id_canale < 0 ) return false;
		
		$db = \FrontController::getDbConnection('main');
		
		$query = 'SELECT id_canale FROM canale WHERE id_canale = '.$db->quote($id_canale).';';
		$res = $db->query($query);
		if (\DB::isError($res)) 
			\Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
	
		$rows = $res->numRows();
		
		if( $rows == 1) return true;
		
		return false;
	}
	
	
	/**
	 * Crea un elenco (array) di oggetti Canale dato un elenco (array) di loro numeri identificativi id_canale del database
	 *
	 * @static
	 * @param array $tipoCanali array contenente ila tipologia del canale
	 * @return mixed array di id_canali se eseguita con successo, false se il canale non esiste
	 */
	function selectCanaliTipo($tipoCanale)
	{
		$db = \FrontController::getDbConnection('main');
		
		$query = 'SELECT id_canale FROM canale WHERE tipo_canale = '.$db->quote($tipoCanale);
		$res = $db->query($query);
		if (\DB::isError($res)) 
			\Error::throwError(_ERROR_CRITICAL,array('msg'=>\DB::errorMessage($res),'file'=>__FILE__,'line'=>__LINE__)); 
	
		$rows = $res->numRows();
		if( $rows == 0) return false;

		$elenco_canali = array();
		while ($res->fetchInto($row))
		{
			//var_dump($row);
			$elenco_canali[] = $row[0];
		}
		$res->free();
		
		return $elenco_canali;
		
	}
	

	/**
	 * compara per nome due canali
	 * 
	 * @static
	 */
	function compareByName($a, $b)
	{
		$nomea = strtolower($a['nome']);
		$nomeb = strtolower($b['nome']);
		return strnatcasecmp($nomea, $nomeb);
	}	
}