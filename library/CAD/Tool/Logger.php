<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.12.15
 * Time: 18:23
 */

class CAD_Tool_Logger {
    /**  @var Object, war als member für die injection eines externen loggers geplant, @TODO...    */
    private $oLogger = null;

    /** @var null */
    private $_rLogFileHandle = null;

    /** @var array */
    private $_aConstNames = array();

    const EMERG = Zend_LOG::EMERG;

    const ALERT = Zend_Log::ALERT;

    const CRIT = Zend_Log::CRIT;

    const ERR = Zend_Log::ERR;

    const WARN = Zend_Log::WARN;

    const NOTICE = Zend_Log::NOTICE;

    const INFO = Zend_Log::INFO;

    const DEBUG = Zend_Log::DEBUG;

    /** @var int */
    private $_iMinLogLevel = self::ERR;
    /** @var int */
    private $_iMaxLogLevel = self::CRIT;

    /** @var string */
    private $_sCurrentInitDate = null;

    /** @var string */
    private $_sLogFileName = null;

    /** @var Zend_Config */
    private $_oConfig = null;

    /** @var string */
    private $_iCurrentLogLevel = null;

    /** @var string */
    private $_sCurrentLogMessage = null;

    /** @var bool */
    private $_bServiceInitialized = false;

    /**
     * CTOR
     */
    public function __construct()
    {
        $this->_init();
    }

    /**
     * DTOR
     */
    public function __destruct()
    {
        if (true === is_resource($this->_getLogFileHandle())) {
            fclose($this->_getLogFileHandle());
        }
    }

    /**
     * initialisierung des Services himself
     *
     * @return \CAD_Tool_Logger
     */
    private function _init()
    {
        $this->setCurrentInitDate(date('Y-m-d H:i:s'));
        $this->_prepareConstNames();
        $this->_parseConfig();
        $this->setLogFileName($this->_extendFileNameWithDate($this->getLogFileName()));
        $this->_prepareFilePath();
        $this->_setServiceInitialized(true);

        return $this;
    }

    /**
     * verarbeitet eine eventuell vorhandene config
     *
     * @return \CAD_Tool_Logger
     */
    private function _parseConfig()
    {
        $sIniFileName = APPLICATION_PATH . '/configs/logger.ini';
        if (true === is_readable($sIniFileName)) {
            $this->setConfig(new Zend_Config_Ini($sIniFileName, APPLICATION_ENV));
            $this->setLogFileName(CAD_Tool_Extractor::extractOverPath($this->getConfig(), 'logger->filePathName'));
            $iMinLogLevel = CAD_Tool_Extractor::extractOverPath($this->getConfig(), 'logger->minLogLevel');
            $iMaxLogLevel = CAD_Tool_Extractor::extractOverPath($this->getConfig(), 'logger->maxLogLevel');

            if (true === is_numeric($iMinLogLevel)) {
                $this->setMinLogLevel($iMinLogLevel);
            }

            if (true === is_numeric($iMaxLogLevel)) {
                $this->setMaxLogLevel($iMaxLogLevel);
            }
        }
        return $this;
    }

    /**
     * bereitet das member array mit den namen der Constanten zu den LogLevels vor
     *
     * @return CAD_Tool_Logger
     */
    private function _prepareConstNames()
    {
        $oReflection = new ReflectionClass(get_called_class());
        $aConstants = $oReflection->getConstants();

        $this->_setConstNames(array_flip($aConstants));
        return $this;
    }

    /**
     * legt den eventuell benötigten Dateipfad rekursiv an und versieht ihn mit schreib und leserechten
     *
     * @return CAD_Tool_Logger
     */
    private function _prepareFilePath()
    {
        $sPathName = dirname($this->getLogFileName());

        if (false === file_exists($sPathName)
            && true === @mkdir($sPathName, true)
        ) {
            chmod($sPathName, 0755);
        }
        return $this;
    }

    /**
     * erweitert den Dateinamen um das datum, an dem der logger gestartet wurde
     *
     * @param string $sFilePathName der name und evtl. Pfad zur datei
     *
     * @return string
     */
    private function _extendFileNameWithDate($sFilePathName)
    {
        $sNewFilePathName = '';
        $aFileInfo = pathinfo($sFilePathName);

        if (true === isset($aFileInfo['dirname'])
            && '.' != substr($aFileInfo['dirname'], 0, 1)
        ) {
            $sNewFilePathName = $aFileInfo['dirname'] . '/';
        }

        $sNewFilePathName .= $aFileInfo['filename'] . '_' .
            date('Y_m_d', strtotime($this->getCurrentInitDate()));

        if (true === isset($aFileInfo['extension'])) {
            $sNewFilePathName .= '.' . $aFileInfo['extension'];
        }

        return $sNewFilePathName;
    }

    /**
     * öffentliche Funktion zum loggen einer Nachricht
     *
     * @param string $sMessage die Nachricht
     * @param int $iLogLevel der LogLevel default ist self::INFO
     *
     * @return CAD_Tool_Logger
     */
    public function log($sMessage, $iLogLevel = self::INFO)
    {
        if ($iLogLevel >= $this->getMaxLogLevel()
            && $iLogLevel <= $this->getMinLogLevel()
        ) {
            $this->_setCurrentLogLevel($iLogLevel);
            $this->_setCurrentLogMessage($sMessage);
            $this->_manageLogMessage();
        }
        return $this;
    }

    /**
     * verarbeitet intern die Nachricht entsprechend dem LogLevel
     *
     * @return CAD_Tool_Logger
     *
     * @throws Zend_File_Transfer_Exception
     */
    private function _manageLogMessage()
    {
        if (true === is_null($this->_getLogFileHandle())) {
            $this->_initLogFile();
        }

        $this->_writeLogMessage();
        return $this;
    }

    /**
     * legt die Nachricht entsprechend ab
     *
     * @return CAD_Tool_Logger
     */
    private function _writeLogMessage()
    {
        $sLogLevelName = $this->_convertLogLevelToString();
        $sPrefix = date("Y-m-d H:i:s") . '|' . $sLogLevelName . '|';
        fwrite($this->_getLogFileHandle(), $sPrefix . $this->_getCurrentLogMessage() . PHP_EOL);

        return $this;
    }

    /**
     * konvertiert den aktuellen LogLevel in seinen Constanten Namen
     *
     * @return string
     */
    private function _convertLogLevelToString()
    {
        $sLogLevelName = 'undefined';
        $aConstNames = $this->_getConstNames();

        if (true === isset($aConstNames[$this->_getCurrentLogLevel()])) {
            $sLogLevelName = $aConstNames[$this->_getCurrentLogLevel()];
        }
        return $sLogLevelName;
    }

    /**
     * initialisiert den Dateinamen zum Loggen
     *
     * @return CAD_Tool_Logger
     *
     * @throws Zend_File_Transfer_Exception
     */
    private function _initLogFile()
    {
        $rFileHandle = fopen($this->getLogFileName(), 'a+');

        if (true === is_resource($rFileHandle)) {
            $this->_setLogFileHandle($rFileHandle);
        } else {
            throw new Zend_File_Transfer_Exception(
                'Logfile ' . basename($this->getLogFileName()) . ' konnte nicht geöffnet werden!'
            );
        }
        return $this;
    }

    /**
     * ist der Service initialisiert worden?
     *
     * @return boolean
     */
    private function _isServiceInitialized()
    {
        return $this->_bServiceInitialized;
    }

    /**
     * setter für den status der initialisierung des services
     *
     * @param boolean $bServiceInitialized kommentar für cs ...
     *
     * @return CAD_Tool_Logger
     */
    private function _setServiceInitialized($bServiceInitialized)
    {
        $this->_bServiceInitialized = $bServiceInitialized;
        return $this;
    }

    /**
     * getter für den Maximal zu loggenden Level
     *
     * @return int
     */
    public function getMaxLogLevel()
    {
        return $this->_iMaxLogLevel;
    }

    /**
     * setter für den maximal zu loggenden level
     *
     * @param int $iMaxLogLevel kommentar für cs ...
     *
     * @return CAD_Tool_Logger
     */
    public function setMaxLogLevel($iMaxLogLevel)
    {
        $this->_iMaxLogLevel = $iMaxLogLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinLogLevel()
    {
        return $this->_iMinLogLevel;
    }

    /**
     * @param int $iMinLogLevel
     *
     * @return CAD_Tool_Logger
     */
    public function setMinLogLevel($iMinLogLevel)
    {
        $this->_iMinLogLevel = $iMinLogLevel;
        return $this;
    }

    /**
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->_oConfig;
    }

    /**
     * @param Zend_Config $oConfig
     *
     * @return CAD_Tool_Logger
     */
    public function setConfig($oConfig)
    {
        $this->_oConfig = $oConfig;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentInitDate()
    {
        return $this->_sCurrentInitDate;
    }

    /**
     * @param string $sCurrentInitDate
     *
     * @return CAD_Tool_Logger
     */
    public function setCurrentInitDate($sCurrentInitDate)
    {
        $this->_sCurrentInitDate = $sCurrentInitDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogFileName()
    {
        return $this->_sLogFileName;
    }

    /**
     * @param string $sLogFileName
     *
     * @return CAD_Tool_Logger
     */
    public function setLogFileName($sLogFileName)
    {
        $this->_sLogFileName = $sLogFileName;
        return $this;
    }

    /**
     * @return null
     */
    public function getLogger()
    {
        return $this->oLogger;
    }

    /**
     * @param null $oLogger
     *
     * @return CAD_Tool_Logger
     */
    public function setLogger($oLogger)
    {
        $this->oLogger = $oLogger;
        return $this;
    }

    /**
     * @return resource
     */
    private function _getLogFileHandle()
    {
        return $this->_rLogFileHandle;
    }

    /**
     * @param resource $rLogFileHandle
     *
     * @return CAD_Tool_Logger
     */
    private function _setLogFileHandle($rLogFileHandle)
    {
        $this->_rLogFileHandle = $rLogFileHandle;
        return $this;
    }

    /**
     * @return string
     */
    private function _getCurrentLogMessage()
    {
        return $this->_sCurrentLogMessage;
    }

    /**
     * @param string $sCurrentLogMessage
     *
     * @return CAD_Tool_Logger
     */
    private function _setCurrentLogMessage($sCurrentLogMessage)
    {
        $this->_sCurrentLogMessage = $sCurrentLogMessage;
        return $this;
    }

    /**
     * @return int
     */
    private function _getCurrentLogLevel()
    {
        return $this->_iCurrentLogLevel;
    }

    /**
     * @param int $iCurrentLogLevel
     *
     * @return CAD_Tool_Logger
     */
    private function _setCurrentLogLevel($iCurrentLogLevel)
    {
        $this->_iCurrentLogLevel = $iCurrentLogLevel;
        return $this;
    }

    /**
     * @return array
     */
    private function _getConstNames()
    {
        return $this->_aConstNames;
    }

    /**
     * @param array $aConstNames
     *
     * @return CAD_Tool_Logger
     */
    private function _setConstNames($aConstNames)
    {
        $this->_aConstNames = $aConstNames;
        return $this;
    }

}