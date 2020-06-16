<?php
/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Send email of ovewrdue booked out items
 *
 * @package AccesstoMemory
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */
class reportsReportBookOutEmailAction extends sfAction
{
    public static $NAMES = array('className', 'dateStart', 'dateEnd', 'dateOf', 'limit');
    
    protected function addField($name)
    {
        switch ($name) {
            case 'dateDue':
                $choices = array(
                    'CREATED_AT' => $this->context->i18n->__('Creation'),
                    'UPDATED_AT' => $this->context->i18n->__('Revision'),
                    'both' => $this->context->i18n->__('Both')
                );
                $this->form->setValidator($name, new sfValidatorChoice(array(
                    'choices' => array_keys($choices)
                )));
                $this->form->setWidget($name, new arWidgetFormSelectRadio(array(
                    'choices' => $choices,
                    'class' => 'radio inline'
                )));
                break;
        }
    }
    public function execute($request)
    {
		// Check authorization
		if ((!$this->context->user->isAdministrator()) && (!$this->context->user->isSuperUser()) && (!$this->context->user->isAuditUser())) {
			QubitAcl::forwardUnauthorized();
		}

        $this->form = new sfForm;
        $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);
        foreach ($this::$NAMES as $name) {
            $this->addField($name);
        }
        $defaults = array(
            'className' => 'QubitBookoutObject',
            'dateStart' => date('Y-m-d', strtotime('-1 month')),
            'dateEnd' => date('Y-m-d'),
            'dateOf' => 'CREATED_AT',
            'publicationStatus' => 'all',
            'limit' => '10',
            'sort' => 'updatedDown'
        );
        
        $this->form->bind($request->getRequestParameters() + $request->getGetParameters() + $defaults);
        if ($this->form->isValid()) {
            $this->overdue = self::doSearchAndSend();
            self::doMail();
        }
    }
    
    public function doSearchAndSend()
    {
        $criteria = new Criteria;
        
        $criteria->addJoin(QubitBookoutObject::ID, QubitBookoutObjectI18n::ID);
        $criteria->addJoin(QubitBookoutObject::DISPATCHER_ID, QubitUser::USERNAME);
        $criteria->addJoin(QubitBookoutObjectI18n::ID, QubitObject::ID);
        $criteria->addJoin(QubitBookoutObject::ID, QubitObject::ID);
        
        $criteria->add(QubitObject::CLASS_NAME, 'QubitBookoutObject');
        $criteria->addSelectColumn(QubitUser::USERNAME);
        $criteria->addSelectColumn(QubitUser::EMAIL);
        $criteria->addSelectColumn(QubitBookoutObjectI18n::TIME_PERIOD);
        $criteria->addSelectColumn(QubitBookoutObjectI18n::NAME);
        $criteria->addSelectColumn(QubitBookoutObject::ID);
        
        $dbMap = Propel::getDatabaseMap($criteria->getDbName());
        $db    = Propel::getDB($criteria->getDbName());
        
        $con = Propel::getConnection($criteria->getDbName(), Propel::CONNECTION_READ);
        
        $stmt = null;
        
        if ($criteria->isUseTransaction())
            $con->beginTransaction();
        
        try {
            
            $params = array();
            $sql    = BasePeer::createSelectSql($criteria, $params);
            
            $stmt = $con->prepare($sql);
            BasePeer::populateStmtValues($stmt, $params, $dbMap, $db);
            
            $stmt->execute();
            
            if ($criteria->isUseTransaction())
                $con->commit();
            
        }
        catch (Exception $e) {
            if ($stmt)
                $stmt = null; // close
            if ($criteria->isUseTransaction())
                $con->rollBack();
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException($e);
        }
        return $stmt;
    }
    
    public function doMail()
    {
        foreach ($this->overdue as $result)
            $vDay = substr($result["TIME_PERIOD"], 0, strpos($result["TIME_PERIOD"], "/"));
        $vRes    = substr($result["TIME_PERIOD"], strpos($result["TIME_PERIOD"], "/") + 1);
        $vMonth  = substr($vRes, 0, strpos($vRes, "/"));
        $vYear   = substr($vRes, strpos($vRes, "/") + 1, 4);
        $s       = $vYear . "/" . $vMonth . "/" . $vDay . " 23.59.59";
        $dueDate = strtotime($s);
        $dueDate = date('Y-m-d H:i:s', strtotime('+1 day', $dueDate));
        echo $dueDate . "<br>";
        $datetime = date('Y-m-d H:i:s');
        $diff     = strtotime($datetime) - strtotime($dueDate);
        echo $diff . "<br>";
        $mailBody = "";
        if ($diff >= 0) {
            $bookOutSubjectId = QubitRelation::getObjectsBySubjectId($result["ID"]);
            
            if (isset($bookOutSubjectId)) {
                foreach ($bookOutSubjectId as $relation) {
                    $informationObjectsBookOut = QubitInformationObject::getById($relation->objectId);
                    $mailBody = "Identifier: ".$informationObjectsBookOut;
                }
                if (isset($informationObjectsBookOut)) {
                    $mailBody = "Identifier: ".$informationObjectsBookOut."<br";
                } else {
                    $mailBody = "Identifier: "."<br";
                }
            }
            
            if (isset($result["NAME"])) {
                $mailBody = $mailBody." Title: ".$result["NAME"]."<br";
            } else {
                $mailBody = $mailBody." Title: "."<br";
            }
            if (isset($result["USERNAME"])) {
                $mailBody = $mailBody." Dispatcher: ".$result["USERNAME"]."<br";
            } else {
                $mailBody = $mailBody." Dispatcher: "."<br";
            }
            if (isset($result["EMAIL"])) {
                $mailBody = $mailBody." e-Mail: ".$result["EMAIL"]."<br";
            } else {
                $mailBody = $mailBody." e-Mail: "."<br";
            }
            if (isset($result["TIME_PERIOD"])) {
                $mailBody = $mailBody." Due date: ".$result["TIME_PERIOD"]."<br";
            } else {
                $mailBody = $mailBody." Due date: "."<br";
            }
            if (isset($result["ID"])) {
                $mailBody = $mailBody." ID: ".$result["ID"]."<br";
            } else {
                $mailBody = $mailBody." ID: "."<br";
            }
        }
        $this->sendEmail("johan.pieterse@sita.co.za", "johanpiet@johanpiet", "Overdue booked out Archival Descriptions", $mailBody);
    }
    
    /**
     * Library to facilitate email messages being sent out
     *
     * @param string $mailFrom - Email source
     * @param string $mailTo - Email destination
     * @param string $subject - The subject of the email message
     */
    
    public static function sendEmail($mailFrom, $mailTo, $subject, $Body)
    {
        $text = null;
        $html = null;
        
        try {
            /*
             * Load connection for mailer
             */
            $connection = Swift_SmtpTransport::newInstance()->setUsername("johanpiet")->setPassword("Julie&0707");
            
            // setup connection/content
            $mailer  = Swift_Mailer::newInstance($connection);
            $message = Swift_Message::newInstance()->setSubject($subject)->setTo($mailTo);
 
            $message->setBody($Body, 'text/html');
            
            // update the from address line to include an actual name
            if (is_array($mailFrom) and count($mailFrom) == 2) {
                $mailFrom = array(
                    $mailFrom['email'] => $mailFrom['name']
                );
            }
            
            // Send
            $message->setFrom($mailFrom);
            $mailer->send($message);
        }
        catch (Exception $e) {
            throw new sfException('Error sending email out - ' . $e->getMessage());
        }
    }
}
