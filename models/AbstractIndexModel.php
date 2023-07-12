<?php
abstract class AbstractIndexModel
{
    /**
     * Database connect
     * @var
     */
    protected $db;

    /**
     * @var
     */
    protected $validationModel;

    /**
     * @var
     */
    protected  $errorModel;

    /**
     * AbstractIndexModel constructor.
     */
    public function __construct()
    {
        $this->db = new MySqlDbWorkModel();
        $this->validationModel = new ValidationModel();
        $this->errorModel = new ErrorModel();
    }

}