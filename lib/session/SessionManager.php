     <?php
     abstract class SessionManager {
    
    public $session_name;
     public $lifeTime = 3600;
    
     public $id;
    
     abstract public function open($savePath, $sessName);
     abstract public function close();
     abstract public function read($sessID);
     abstract public function write($sessID,$sessData);
     abstract public function destroy($sessID);
     abstract public function gc($sessMaxLifeTime);
    
     public function __construct() {
    
     if ( !session_set_save_handler(array(&$this,'open'),
     array(&$this,'close'),
     array(&$this,'read'),
     array(&$this,'write'),
     array(&$this,'destroy'),
     array(&$this,'gc') ) ) {
     throw new Exception('Erreur lors de l\'init des sessions !');
     }
    
     session_start();
    
     }
     }
    
     class SQLSessionManager extends SessionManager {
    
     private $db;
    
     public function __destruct() {
     session_write_close();
     }
    
     public function open($savePath, $sessName) {
    
     $this->db = mysql::GetInstance();
     return true;
    
     }
    
     public function close() {
    
     $this->gc(ini_get('session.gc_maxlifetime'));
     unset($this->db);
    
     }
    
     public function read($sessID) {
    
     $this->id = $sessID;
     $this->db->prepare("SELECT data FROM session WHERE id = '{1}' AND expires > {2}",
     $sessID, time() );
     $this->db->query();
    
     return ( ( $row = $this->db->fetch_row() ) !== FALSE ) ? $row[0] : ' ';
    
     }
    
     public function write($sessID,$sessData) {
    
     $this->id = $sessID;
     $newExp = time() + $this->lifeTime;
     $this->db->prepare("INSERT INTO session (id, data, expires) VALUES ('{1}', '{2}', {3})
     ON DUPLICATE KEY UPDATE data = '{2}', expires = {3}",
     $sessID, $sessData, $newExp);
     $this->db->query();
    
     return TRUE;
    
     }
    
     public function destroy($sessID) {
    
     $this->db->prepare("DELETE FROM session WHERE id = '{1}'", $sessID);
     $this->db->query();
     return ( $this->db->affected_rows() === 1 ) ? TRUE : FALSE;
    
     }
    
     public function gc($sessMaxLifeTime) {
    
     $this->db->prepare("DELETE FROM session WHERE ( UNIX_TIMESTAMP(expires) - UNIX_TIMESTAMP(NOW()) ) > '{1}' ", $sessMaxLifeTime);
     $this->db->query();
     return $this->db->affected_rows();
    
     }
    
     }
     ?>