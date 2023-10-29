<?php declare(strict_types=1);

require __DIR__ . "/../../app/includes/connection.inc.php";

use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase {
    public $host = "localhost"; // NEED GLOBAL VARIABLES HERE, FOOL
    public $user = "root";
    public $password = ""; 
    public $db = "caesuralitmag_data"; 

    // Checks if Database's constructor works properly
    public function testConstructor() {
        $database = new Database($this->host, $this->user, $this->password, $this->db); 

        $this->assertSame($this->host, $database->host);
        $this->assertSame($this->user, $database->user);
        $this->assertSame($this->password, $database->password);
        $this->assertSame($this->db, $database->db);
    }

    // Checks if amount of data retrieved is equal to the amount expected
    public function testSelectAll_1() {
        $database = new Database($this->host, $this->user, $this->password, $this->db);  

        $results = $database->selectAll("CONTACT"); 
        $count = 0; 

        foreach ($results as $contact) {
            $count++; 
        }

        $this->assertCount($count, $results); 
    }

    // Checks if function is selecting all attributes
    public function testSelectAll_2() {
        $database = new Database($this->host, $this->user, $this->password, $this->db);
        
        $results = $database->selectAll("WORK"); 

        // Only thing about this is that it will break if more columns are added to work
        foreach ($results as $work) {
            $this->assertCount(7, $work); 
        }
    }

    // Selects all contributors and confirms each contributor owns a work
    public function testSelectRow_1() {
        $database = new Database($this->host, $this->user, $this->password, $this->db); 

        $results = $database->selectAll("CONTRIBUTOR"); 

        foreach ($results as $contributor) {
            $work = $database->selectRow("WORK", "CON_ID", $contributor["CON_ID"]); 
            $this->assertNotNull($work); 
        }
    }

    // Checks existence of array keys in media_type where ID = 1
    public function testSelectRow_2() {
        $database = new Database($this->host, $this->user, $this->password, $this->db);

        $results = $database->selectRow("MEDIA_TYPE", "MEDIA_ID", 1); 

        foreach ($results as $media) {
            $this->assertArrayHasKey("MEDIA_ID", $media); 
            $this->assertArrayHasKey("MEDIA_NAME", $media); 
        }
    }

    // Checks if column GUIDE_DESCRIPT only contains string data
    public function testSelectColumn_1() {
        $database = new Database($this->host, $this->user, $this->password, $this->db);

        $results = $database->selectColumn("GUIDELINE", "GUIDE_DESCRIPT"); 

        foreach ($results as $guideline) {
            $this->assertContainsOnly("string", $guideline); 
        }
    }

    // Checks if each contributor ID is unique 
    public function testSelectColumn_2() {
        $database = new Database($this->host, $this->user, $this->password, $this->db);

        $results = $database->selectColumn("CONTRIBUTOR", "CON_ID"); 
        $ids = []; 

        foreach ($results as $contributor) {
            $this->assertNotContains($contributor["CON_ID"], $ids); 
            array_push($ids, $contributor["CON_ID"]);
            fwrite(STDERR, print_r($ids, true)); 
        }
    }

    
}


?>

