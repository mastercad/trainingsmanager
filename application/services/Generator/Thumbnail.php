<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.05.17
 * Time: 22:57
 */

class Service_Generator_Thumbnail {

    private $sourceFilePathName = null;

    private $destinationFilePathName = null;

    private $thumbHeight = null;

    private $thumbWidth = null;

    private $priorityHeight = false;

    private $priorityWidth = false;

    private $sourceImageHeight = null;

    private $sourceImageWidth = null;

    private $destinationStartX = 0;

    private $destinationStartY = 0;

    private $ratio = null;

    private $factorHeight = 1;

    private $factorWidth = 1;

    private $newHeight = null;

    private $newWidth = null;

    private $sourceImageType = null;

    private $sourceImageSize = null;

    private $sourceImage = null;

    private $destinationImage = null;

    private $defineHeader = true;

    private $map = [
        'width' => 'thumbWidth',
        'height' => 'thumbHeight',
        'prioWidth' => 'priorityWidth',
        'prioHeight' => 'priorityHeight',
        'file' => 'sourceFilePathName'
    ];
    private $validImageTypes = [
        1,
        2,
        3,
        'png',
        'jpeg',
        'jpg',
        'bmp',
        'gif'
    ];

    /**
     * generates thumb as image
     *
     * @param null $params
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function generate($params = null) {

        return $this->parseParams($params)
            ->prepareSourceImageData()
            ->validateSourceImagePath()
            ->parseSourceImageData()
            ->calculateRatio()
            ->prepareThumbData()
            ->calculateFactorHeight()
            ->calculateFactorWidth()
            ->calculateDestinationImageDimensions()
            ->calculateDestinationStartPoints()
            ->generateThumbnail();
    }

    /**
     * generates thumb as string
     *
     * @param null $params
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateImageString($params = null) {
        $this->setDefineHeader(false)
            ->parseParams($params)
            ->prepareSourceImageData()
            ->validateSourceImagePath()
            ->parseSourceImageData()
            ->calculateRatio()
            ->prepareThumbData()
            ->calculateFactorHeight()
            ->calculateFactorWidth()
            ->calculateDestinationImageDimensions()
            ->calculateDestinationStartPoints();

        ob_start();
        $this->generateThumbnail();
        $imageString = ob_get_clean();
        ob_end_clean();

        $sourceImageType = pathinfo($this->getSourceFilePathName(), PATHINFO_EXTENSION);
        return 'data:image/'.$sourceImageType.';base64,'.base64_encode($imageString);
    }

    private function generateThumbnail() {

        $this->setDestinationImage(imagecreatetruecolor($this->getThumbWidth(), $this->getThumbHeight()));

        switch ($this->getSourceImageType()) {

            /* gif */
            case 1: {
                $this->createGifImage();
                break;
            }
            /* jpeg */
            case 2: {
                $this->createJpgImage();
                break;
            }
            /* png */
            case 3: {
                $this->createPngImage();
                break;
            }
            default: {
                $this->generateErrorImage("Fehler beim Verarbeiten");
                break;
            }
        }
        return $this;
    }

    private function prepareSourceImageData() {
        if (!empty($this->getSourceFilePathName())
            && !preg_match('/[\/|\.]/', $this->getSourceFilePathName())
        ) {
            $this->setSourceFilePathName(base64_decode($this->getSourceFilePathName()));
        }
        return $this;
    }

    private function validateSourceImagePath() {

        if (false === is_readable($this->getSourceFilePathName())) {
            throw new Exception('File not found!');
        }
        return $this;
    }

    private function parseSourceImageData() {

        $fileInformation = getimagesize($this->getSourceFilePathName());
        $this->setSourceImageWidth($fileInformation[0]);
        $this->setSourceImageHeight($fileInformation[1]);
        $this->setSourceImageType($fileInformation[2]);
        $this->setSourceImageSize(filesize($this->getSourceFilePathName()));
        return $this;
    }

    /**
     * @return $this
     */
    private function prepareThumbData() {

        if (!$this->getThumbWidth()
            && $this->getThumbHeight()
        ) {
            $this->setThumbWidth($this->getThumbHeight() / $this->getRatio());
        }

        if (!$this->getThumbHeight() &&
            $this->getThumbWidth()
        ) {
            $this->setThumbHeight($this->getThumbWidth() / $this->getRatio());
        }

        if (!$this->getThumbWidth()
            && !$this->getThumbHeight()
        ) {
            $this->setThumbWidth($this->getSourceImageWidth() - 1);
            $this->setThumbHeight($this->getSourceImageHeight() - 1);
        }
        return $this;
    }

    private function calculateRatio() {

        $this->setRatio($this->getSourceImageWidth() / $this->getSourceImageHeight());
        return $this;
    }

    private function calculateFactorHeight() {

        if ($this->getSourceImageHeight() > $this->getThumbHeight()) {
            $this->setFactorHeight($this->getThumbHeight() / $this->getSourceImageHeight());
        }
        return $this;
    }

    private function calculateFactorWidth() {

        if ($this->getSourceImageWidth() > $this->getThumbWidth()) {
            $this->setFactorWidth($this->getThumbWidth() / $this->getSourceImageWidth());
        }
        return $this;
    }

    private function calculateDestinationImageDimensions() {

        if ($this->isPriorityWidth()) {
            $this->setNewHeight($this->getSourceImageHeight() * $this->getFactorWidth());
            $this->setNewWidth($this->getSourceImageWidth() * $this->getFactorWidth());
        } else {
            $this->setNewHeight($this->getSourceImageHeight() * $this->getFactorHeight());
            $this->setNewWidth($this->getSourceImageWidth() * $this->getFactorHeight());
        }
        return $this;
    }

    private function calculateDestinationStartPoints() {

        if ($this->getNewHeight() < $this->getThumbHeight()) {
            $this->setDestinationStartY(($this->getThumbHeight() - $this->getNewHeight()) / 2);
        } else {
            $this->setThumbHeight($this->getNewHeight());
        }

        if ($this->getNewWidth() < $this->getThumbWidth()) {
            $this->setDestinationStartX(($this->getThumbWidth() - $this->getNewWidth()) / 2);
        } else {
            $this->setThumbWidth($this->getNewWidth());
        }
        return $this;
    }

    /**
     * iterates given params and set as member, if is known
     *
     * @param $params
     *
     * @return $this
     */
    private function parseParams($params) {
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                if (array_key_exists($key, $this->map)) {
                    $setter = 'set'.ucfirst(($this->map[$key]));
                    $this->$setter($param);
                }
            }
        }
        return $this;
    }

    private function createPngImage() {

        if ($this->isDefineHeader()) {
            header('Content-Type: image/png');
        }

        $black = imagecolorallocate($this->getDestinationImage(), 0, 0, 0);
        imagecolortransparent($this->getDestinationImage(), $black);

        $img_src = imagecreatefrompng($this->getSourceFilePathName());
        imagealphablending($img_src, false);
        imagesavealpha($img_src, true);

        imagecopyresampled(
            $this->getDestinationImage(),
            $img_src,
            $this->getDestinationStartX(),
            $this->getDestinationStartY(),
            0,
            0,
            $this->getNewWidth(),
            $this->getNewHeight(),
            $this->getSourceImageWidth(),
            $this->getSourceImageHeight()
        );

        imagepng($this->getDestinationImage());
        ImageDestroy($img_src);
        ImageDestroy($this->getDestinationImage());

        return $this;
    }

    private function createJpgImage() {

        if ($this->isDefineHeader()) {
            header('Content-Type: image/jpeg');
        }

        $img_src = imagecreatefromjpeg($this->getSourceFilePathName());

        imagecopyresampled(
            $this->getDestinationImage(),
            $img_src,
            $this->getDestinationStartX(),
            $this->getDestinationStartY(),
            0,
            0,
            $this->getNewWidth(),
            $this->getNewHeight(),
            $this->getSourceImageWidth(),
            $this->getSourceImageHeight()
        );

        imagejpeg($this->getDestinationImage(), null, 75);
        ImageDestroy($img_src);
        ImageDestroy($this->getDestinationImage());

        return $this;
    }

    private function createBmpImage() {

        return $this;
    }

    private function createGifImage() {

        if ($this->isDefineHeader()) {
            header('Content-Type: image/gif');
        }

        $black = imagecolorallocate($this->getDestinationImage(), 0, 0, 0);
        imagecolortransparent($this->getDestinationImage(), $black);

        $img_src = imagecreatefromgif($this->getSourceFilePathName());

        /* eventuelle transparenz des originals beibehalten */
        $transparent_index = imagecolortransparent($img_src);

        if ($transparent_index != (-1)) {
            @$transparent_color = imagecolorsforindex($img_src, $transparent_index);
            @$transparent_new = imagecolorallocate($img_src, $transparent_color['red'], $transparent_color['green'],
                $transparent_color['blue']);
            $transparent_new_index = imagecolortransparent($img_src, $transparent_new);
            imagefill($img_src, 0, 0, $transparent_new_index);
        }

        imagecopyresampled(
            $this->getDestinationImage(),
            $img_src,
            $this->getDestinationStartX(),
            $this->getDestinationStartY(),
            0,
            0,
            $this->getNewWidth(),
            $this->getNewHeight(),
            $this->getSourceImageWidth(),
            $this->getSourceImageHeight()
        );

        imagegif($this->getDestinationImage());
        ImageDestroy($img_src);
        ImageDestroy($this->getDestinationImage());

        return $this;
    }

    private function generateErrorImage($errorText) {

        if ($this->isDefineHeader()) {
            header('Content-Type: image/png');
        }

        $black = imagecolorallocate($this->getDestinationImage(), 0, 0, 0);
        imagecolortransparent($this->getDestinationImage(), $black);
        header('Content-Type: image/png');
        imagestring($this->getDestinationImage(), 1, 1, $this->getThumbHeight() / 2, $errorText, $black);
        imagepng($this->getDestinationImage());

        return $this;
    }

    /**
     * @return null
     */
    public function getDestinationFilePathName() {
        return $this->destinationFilePathName;
    }

    /**
     * @param null $destinationFilePathName
     */
    public function setDestinationFilePathName($destinationFilePathName) {
        $this->destinationFilePathName = $destinationFilePathName;
    }

    /**
     * @return null
     */
    public function getSourceFilePathName() {
        return $this->sourceFilePathName;
    }

    /**
     * @param null $sourceFilePathName
     */
    public function setSourceFilePathName($sourceFilePathName) {
        $this->sourceFilePathName = $sourceFilePathName;
    }

    /**
     * @return null
     */
    public function getThumbHeight() {
        return $this->thumbHeight;
    }

    /**
     * @param null $thumbHeight
     */
    public function setThumbHeight($thumbHeight) {
        $this->thumbHeight = $thumbHeight;
    }

    /**
     * @return null
     */
    public function getThumbWidth() {
        return $this->thumbWidth;
    }

    /**
     * @param null $thumbWidth
     */
    public function setThumbWidth($thumbWidth) {
        $this->thumbWidth = $thumbWidth;
    }

    /**
     * @return boolean
     */
    public function isPriorityHeight() {
        return $this->priorityHeight;
    }

    /**
     * @param boolean $priorityHeight
     */
    public function setPriorityHeight($priorityHeight) {
        $this->priorityHeight = $priorityHeight;
    }

    /**
     * @return boolean
     */
    public function isPriorityWidth() {
        return $this->priorityWidth;
    }

    /**
     * @param boolean $priorityWidth
     */
    public function setPriorityWidth($priorityWidth) {
        $this->priorityWidth = $priorityWidth;
    }

    /**
     * @return null
     */
    public function getSourceImageHeight() {
        return $this->sourceImageHeight;
    }

    /**
     * @param null $sourceImageHeight
     */
    public function setSourceImageHeight($sourceImageHeight) {
        $this->sourceImageHeight = $sourceImageHeight;
    }

    /**
     * @return null
     */
    public function getSourceImageWidth() {
        return $this->sourceImageWidth;
    }

    /**
     * @param null $sourceImageWidth
     */
    public function setSourceImageWidth($sourceImageWidth) {
        $this->sourceImageWidth = $sourceImageWidth;
    }

    /**
     * @return null
     */
    public function getSourceImageSize() {
        return $this->sourceImageSize;
    }

    /**
     * @param null $sourceImageSize
     */
    public function setSourceImageSize($sourceImageSize) {
        $this->sourceImageSize = $sourceImageSize;
    }

    /**
     * @return null
     */
    public function getSourceImageType() {
        return $this->sourceImageType;
    }

    /**
     * @param null $sourceImageType
     */
    public function setSourceImageType($sourceImageType) {
        $this->sourceImageType = $sourceImageType;
    }

    /**
     * @return null
     */
    public function getRatio() {
        return $this->ratio;
    }

    /**
     * @param null $ratio
     */
    public function setRatio($ratio) {
        $this->ratio = $ratio;
    }

    /**
     * @return null
     */
    public function getFactorHeight() {
        return $this->factorHeight;
    }

    /**
     * @param null $factorHeight
     */
    public function setFactorHeight($factorHeight) {
        $this->factorHeight = $factorHeight;
    }

    /**
     * @return null
     */
    public function getFactorWidth() {
        return $this->factorWidth;
    }

    /**
     * @param null $factorWidth
     */
    public function setFactorWidth($factorWidth) {
        $this->factorWidth = $factorWidth;
    }

    /**
     * @return null
     */
    public function getNewHeight() {
        return $this->newHeight;
    }

    /**
     * @param null $newHeight
     */
    public function setNewHeight($newHeight) {
        $this->newHeight = $newHeight;
    }

    /**
     * @return null
     */
    public function getNewWidth() {
        return $this->newWidth;
    }

    /**
     * @param null $newWidth
     */
    public function setNewWidth($newWidth) {
        $this->newWidth = $newWidth;
    }

    /**
     * @return int
     */
    public function getDestinationStartX() {
        return $this->destinationStartX;
    }

    /**
     * @param int $destinationStartX
     */
    public function setDestinationStartX($destinationStartX) {
        $this->destinationStartX = $destinationStartX;
    }

    /**
     * @return int
     */
    public function getDestinationStartY() {
        return $this->destinationStartY;
    }

    /**
     * @param int $destinationStartY
     */
    public function setDestinationStartY($destinationStartY) {
        $this->destinationStartY = $destinationStartY;
    }

    /**
     * @return null
     */
    public function getSourceImage() {
        return $this->sourceImage;
    }

    /**
     * @param null $sourceImage
     */
    public function setSourceImage($sourceImage) {
        $this->sourceImage = $sourceImage;
    }

    /**
     * @return null
     */
    public function getDestinationImage() {
        return $this->destinationImage;
    }

    /**
     * @param null $destinationImage
     */
    public function setDestinationImage($destinationImage) {
        $this->destinationImage = $destinationImage;
    }

    /**
     * @return array
     */
    public function getMap() {
        return $this->map;
    }

    /**
     * @param array $map
     */
    public function setMap($map) {
        $this->map = $map;
    }

    /**
     * @return array
     */
    public function getValidImageTypes() {
        return $this->validImageTypes;
    }

    /**
     * @param array $validImageTypes
     */
    public function setValidImageTypes($validImageTypes) {
        $this->validImageTypes = $validImageTypes;
    }

    /**
     * @return boolean
     */
    private function isDefineHeader() {
        return $this->defineHeader;
    }

    /**
     * @param boolean $defineHeader
     *
     * @return $this
     */
    private function setDefineHeader($defineHeader) {
        $this->defineHeader = $defineHeader;
        return $this;
    }

}