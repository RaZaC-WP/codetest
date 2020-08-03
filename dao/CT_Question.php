<?php


namespace CT\DAO;


class CT_Question
{
    private $question_id;
    private $ct_id;
    private $question_num;
    private $question_txt;
    private $modified;

    public function __construct($question_id = null)
    {
        $context = array();
        if (isset($question_id)) {
            $connection = CT_DAO::getConnection();
            $query = "SELECT * FROM {$connection['p']}ct_question WHERE question_id = :question_id";
            $arr = array(':question_id' => $question_id);
            $context = $connection['PDOX']->rowDie($query, $arr);
        }
        CT_DAO::setObjectPropertiesFromArray($this, $context);
    }

    public static function getByMain($ct_id)
    {
        $connection = CT_DAO::getConnection();
        $query = "SELECT * FROM {$connection['p']}ct_question WHERE ct_id = :ctId order by question_num;";
        $arr = array(':ctId' => $ct_id);
        return CT_DAO::createObjectFromArray(self::class, $connection['PDOX']->allRowsDie($query, $arr));
    }

    /**
     * @return mixed
     */
    public function getQuestionId()
    {
        return $this->question_id;
    }

    /**
     * @param mixed $question_id
     */
    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;
    }

    /**
     * @return mixed
     */
    public function getCtId()
    {
        return $this->ct_id;
    }

    /**
     * @param mixed $ct_id
     */
    public function setCtId($ct_id)
    {
        $this->ct_id = $ct_id;
    }

    /**
     * @return mixed
     */
    public function getQuestionNum()
    {
        return $this->question_num;
    }

    /**
     * @param mixed $question_num
     */
    public function setQuestionNum($question_num)
    {
        $this->question_num = $question_num;
    }

    function getNextQuestionNumber() {

        $connection = CT_DAO::getConnection();
        $query = "SELECT MAX(question_num) as lastNum FROM {$connection['p']}ct_question WHERE ct_id = :ctId";
        $arr = array(':ctId' => $this->getCtId());
        $lastNum = $connection['PDOX']->rowDie($query, $arr)["lastNum"];
        return $lastNum + 1;
    }

    /**
     * @return mixed
     */
    public function getQuestionTxt()
    {
        return $this->question_txt;
    }

    /**
     * @param mixed $question_txt
     */
    public function setQuestionTxt($question_txt)
    {
        $this->question_txt = $question_txt;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function isNew()
    {
        $question_id = $this->getQuestionId();
        return !(isset($question_id) && $question_id > 0);
    }

    public function save() {
        global $CFG;
        $connection = CT_DAO::getConnection();
        $currentTime = new \DateTime('now', new \DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");
        if($this->isNew()) {
            $this->setQuestionNum($this->getNextQuestionNumber());
            $query = "INSERT INTO {$connection['p']}ct_question  "
                . "(`ct_id`, `question_num`, `question_txt`, `modified` ) "
                . "VALUES (:ctId, :question_num, :question_txt, :modified )";
        } else {
            $query = "UPDATE {$connection['p']}ct_question set "
                . "`ct_id` = :ctId, "
                . "`question_num` = :question_num, "
                . "`question_txt` = :question_txt, "
                . "`modified` = :modified "
                . "WHERE question_id = :question_id";
        }
        $arr = array(
            ':modified' => $currentTime,
            ':ctId' => $this->getCtId(),
            ':question_num' => $this->getQuestionNum(),
            ':question_txt' => $this->getQuestionTxt(),
        );
        if(!$this->isNew()) $arr[':question_id'] = $this->getQuestionId();
        $connection['PDOX']->queryDie($query, $arr);
        if($this->isNew()) $this->setQuestionId($connection['PDOX']->lastInsertId());
    }


}