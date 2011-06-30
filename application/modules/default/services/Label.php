<?php
class Default_Service_Label extends Issues_ServiceAbstract
{
    protected $_createForm;

    public function getLabelById($id)
    {
        return $this->_mapper->getLabelById($id);
    }

    public function getLabelsByIssue($issue)
    {
        return $this->_mapper->getLabelsByIssue($issue);
    }

    public function getAllLabels()
    {
        return $this->_mapper->getAllLabels();
    }

    public function createLabel($text, $color)
    {
        $label = new Default_Model_Label();
        $label->setText($text)->setColor($color);
        $this->_mapper->insert($label);
        return true;
    }

    public function getCreateForm()
    {
        if (null === $this->_createForm) {
            $this->_createForm = new Default_Form_Label_Create();
        }
        return $this->_createForm;
    }
}
