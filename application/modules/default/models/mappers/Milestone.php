<?php
class Default_Model_Mapper_Milestone extends Issues_Model_Mapper_DbAbstract
{
    protected $_name = 'milestone';
    protected $_modelClass = 'Default_Model_Milestone';

    public function getMilestoneById($id)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from($this->getTableName())
            ->where('milestone_id = ?', $id);
        $sql = $this->_addAclJoins($sql);
        $row = $db->fetchRow($sql);
        return $this->_rowToModel($row);
    }

    public function getAllMilestones($counts = false)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from(array('m'=>$this->getTableName()), 'm.*');

        if ($counts === true) {
            $sql->joinLeft(array('iml'=>'issue_milestone_linker'), 'iml.milestone_id = m.milestone_id')
                ->joinLeft(array('i'=>'issue'), 'iml.issue_id = i.issue_id', 'status')
                ->columns(array('open_count'=>'SUM(CASE WHEN i.status = \'open\' THEN 1 ELSE 0 END)'))
                ->columns(array('closed_count'=>'SUM(CASE WHEN i.status = \'closed\' THEN 1 ELSE 0 END)'))
                ->group('m.milestone_id');
        }

        $sql = $this->_addAclJoins($sql, 'm', 'milestone_id');

        $rows = $db->fetchAll($sql);
        return $this->_rowsToModels($rows);
    }

    public function getMilestonesByIssue($issue)
    {
        $db = $this->getReadAdapter();
        $sql = $db->select()
            ->from(array('iml'=>'issue_milestone_linker'))
            ->join(array('m'=>'milestone'), 'iml.milestone_id = m.milestone_id');

        if ($issue instanceof Default_Model_Issue) {
            $sql->where('iml.issue_id = ?', $issue->getIssueId());
        } else {
            $sql->where('iml.issue_id = ?', (int) $issue);
        }

        $sql = $this->_addAclJoins($sql, 'm', 'milestone_id');
        $rows = $db->fetchAll($sql);
        return $this->_rowsToModels($rows);
    }

    public function insert(Default_Model_Milestone $milestone)
    {
        $data = array(
            'name'      => $milestone->getName(),
            'due_date'  => $milestone->getDueDate()->format('Y-m-d'),
            'private'   => $milestone->isPrivate() ? 1 : 0,
        );

        $db = $this->getWriteAdapter();
        $db->insert($this->getTableName(), $data);
        return $db->lastInsertId();
    }
}
