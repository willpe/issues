<?php
class Default_Service_Issue extends Issues_ServiceAbstract 
{
    protected $_createForm;

    public function getCreateForm()
    {
        if (null === $this->_createForm) {
            $this->_createForm = new Default_Form_Issue_Create();
        }
        return $this->_createForm;
    }

    public function getIssueById($id)
    {
        return $this->_mapper->getIssueById($id);
    }

    public function getAllIssues()
    {
        return $this->_mapper->getAllIssues();
    }

    public function getIssuesByMilestone($milestone, $status = null)
    {
        return $this->_mapper->getIssuesByMilestone($milestone, $status);
    }

    public function filterIssues($status)
    {
        return $this->_mapper->filterIssues($status);
    }

    public function createFromForm(Default_Form_Issue_Create $form)
    {
        if (Zend_Auth::getInstance()->getIdentity()->getRole()->getName() == 'guest') {
            return false; 
        } 
        $issue = new Default_Model_Issue();
        $issue->setTitle($form->getValue('title'))
            ->setDescription($form->getValue('description'))
            ->setStatus('open')
            ->setProject($form->getValue('project'))
            ->setCreatedBy(Zend_Auth::getInstance()->getIdentity());
        return $this->_mapper->insert($issue);
    }

    public function addLabelToIssue($issue, $label)
    {
        // TODO should probably check permissions here
        if (!($issue instanceof Default_Model_Issue)) {
            $issue = $this->_mapper->getIssueById($issue);
        }

        if (!($label instanceof Default_Model_Label)) {
            $label = Zend_Registry::get('Default_DiContainer')->getLabelService()->getLabelById($label);
        }

        $this->_mapper->addLabelToIssue($issue, $label);
    }

    public function removeLabelFromIssue($issue, $label)
    {
        // TODO should probably check permissions here
        if (!($issue instanceof Default_Model_Issue)) {
            $issue = $this->_mapper->getIssueById($issue);
        }

        if (!($label instanceof Default_Model_Label)) {
            $label = Zend_Registry::get('Default_DiContainer')->getLabelService()->getLabelById($label);
        }

        $this->_mapper->removeLabelFromIssue($issue, $label);
    }

    public function countIssuesByLabel(Default_Model_Label $label)
    {
        return $this->_mapper->countIssuesByLabel($label);
    }
}

