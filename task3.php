<?php
/**
 * Class TableCreator
 *
 * This class creates a table 'Test' with specific fields and fills it with random data.
 * It allows retrieving data based on specific criteria.
 */
final class TableCreator
{
    /** @var PDO|null Database connection */
    private $pdo;

    /**
     * Constructor.
     *
     * Executes the create and fill methods upon object instantiation.
     */
    public function __construct()
    {
        // Initialize database connection (replace with your actual database connection details)
        $this->pdo = new PDO('mysql:host=localhost;dbname=task3-db', 'root', '');

        // Execute create and fill methods
        $this->create();
        $this->fill();
    }

    /**
     * Creates a table 'Test' with specific fields.
     *
     * Fields:
     * - id: integer, auto-incremental
     * - script_name: string, 25 characters long
     * - start_time: datetime
     * - end_time: datetime
     * - result: one option from 'normal', 'illegal', 'failed', 'success'
     *
     * This method is accessible only within the class.
     */
    private function create()
    {
        $query = "CREATE TABLE IF NOT EXISTS Test (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    script_name VARCHAR(25),
                    start_time DATETIME,
                    end_time DATETIME,
                    result ENUM('normal', 'illegal', 'failed', 'success')
                  )";
        
        $this->pdo->exec($query);
    }

    /**
     * Fills the table 'Test' with random data.
     *
     * This method is accessible only within the class.
     */
    private function fill()
    {
        // Generate random data and insert into the table
        $scriptNames = ['Script A', 'Script B', 'Script C'];
        $results = ['normal', 'illegal', 'failed', 'success'];

        for ($i = 0; $i < 10; $i++) {
            $scriptName = $scriptNames[array_rand($scriptNames)];
            $startTime = date('Y-m-d H:i:s', mt_rand(strtotime('2020-01-01'), time()));
            $endTime = date('Y-m-d H:i:s', mt_rand(strtotime('2020-01-01'), time()));
            $result = $results[array_rand($results)];

            $query = "INSERT INTO Test (script_name, start_time, end_time, result)
                      VALUES (:script_name, :start_time, :end_time, :result)";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':script_name', $scriptName);
            $stmt->bindParam(':start_time', $startTime);
            $stmt->bindParam(':end_time', $endTime);
            $stmt->bindParam(':result', $result);
            $stmt->execute();
        }
    }

    /**
     * Retrieves data from the table 'Test' based on the result criterion.
     *
     * Accessible from outside the class.
     *
     * @return array Fetched rows matching the criterion
     */
    public function get()
    {
        $query = "SELECT * FROM Test WHERE result IN ('normal', 'success')";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Example usage:
$tableCreator = new TableCreator();
$data = $tableCreator->get();
print_r($data);
?>
