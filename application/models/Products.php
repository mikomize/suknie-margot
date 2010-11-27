<?php
class Default_Model_Products {
  
  const WITH_PHOTOS = 2;
  const WITH_TAGS = 4;
  const WITH_SIZES = 8;
  
  public static $categoryList = array(
    '1' => 'Wieczorowe',
    '2' => 'Na dzień',
    '3' => 'Dodatki'
  );
  
  public static $tagsList = array(
    '1' => 'Promocja',
    '2' => 'Nowość',
    '3' => 'Mini',
    '4' => 'Midi',
    '5' => 'Maxi',
    '8' => 'Dzianina',
    '9' => 'czerń i brąz',
    '10'=> 'srebro i szarość',
    '11'=> 'złoto i żółty',
    '12'=> 'odcienie czerwieni',
    '13'=> 'odcienie niebieskiego',
    '14'=> 'odcienie zieleni',
    '21'=> 'odcienie różu i fioletu',
    '15'=> 'biel i ecru',
    '16' => 'żakiety',
    '17' => 'bolerka',
    '18' => 'chusty',
    '19' => 'torebki',
    '20' => 'upięcia kwiatowe'
  );
  
  public static $sizesList = array(
    '36' => '34-36',
    '38' => '38',
    '40' => '40',
    '42' => '42',
    '44' => '44',
    '46' => '46',
    '48' => '48',
    '50' => '50'
  );
  
  public function __construct() 
  {
    $this->db = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db');
    $this->db->query('SET NAMES utf8');
    $this->db->query('SET CHARACTER SET utf8');
    $this->dbCache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cachemanager')->getCache('db');
  }
 
  private function getProductCacheKey($productId, $flags) {
    return 'product_' . $productId . '_' . $flags;
  }

  private function getProductCacheTag($productId) {
    return 'product_' . $productId;
  }

  private function getProductsCacheKey($flags) {
    return 'products_' . $flags;
  }

  private function getProductsCacheTag() {
    return 'products';
  }
  
  private function invalidateProductCache($productID) {
    $this->dbCache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->getProductCacheTag($productId)));
  }

  private function invalidateProductsCache() {
    $this->dbCache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->getProductsCacheTag()));
  }
   
  public function getProductCached($productId, $flags = 0) {
    $cacheKey = $this->getProductCacheKey($productId, $flags);
    if(false === $product = $this->dbCache->load($cacheKey)) {
      $product = $this->getProduct($productId, $flags);
      $this->dbCache->save($product, $cacheKey, array($this->getProductCacheTag($productId)));
    } 
    return $product;
  }

  private function getProduct($productId, $flags = 0) 
  {
    $query = 'SELECT id,name,category_id,price,primary_photo_id,length,description FROM products WHERE id = ?';
    $result = $this->db->fetchRow($query, $productId);
    if($flags & self::WITH_TAGS) {
      $result['tags'] = $this->getTags($productId);
    }
    if($flags & self::WITH_SIZES) {
      $result['sizes'] = $this->getSizes($productId);
    }
    if($flags & self::WITH_PHOTOS) {
      $result['photos'] = $this->getPhotos($productId);
      $result['primary_photo_id'] = current($result['photos']); // hak
    }
    $result['category'] = self::$categoryList[$result['category_id']];
    return $result;
  }
  
  public function getProductsCached($flags = 0) {
    $cacheKey = $this->getProductsCacheKey($flags);
    if(false === $products = $this->dbCache->load($cacheKey)) {
      $products = $this->getProducts($flags);
      $this->dbCache->save($products, $cacheKey, array($this->getProductsCacheTag()));
    } 
    return $products;
  }

  private function getProducts($flags = 0) 
  {
    $query = 'SELECT id FROM products';
    $result = $this->db->fetchCol($query);
    $products = array();
    foreach($result as $product_id) {
      $product = $this->getProductCached($product_id, $flags);
      $products[$product_id] = $product;
    }
    return $products;
  }
  
  private function getTags($productId) 
  {
    $query = 'SELECT tag_id FROM tags WHERE product_id = ?';
    $result = $this->db->fetchCol($query, $productId);
    $tags = array();
    foreach($result as $tagId ) {
      $tags[] = $tagId;
    }
    return $tags;
  }
  
  private function getSizes($productId) 
  {
    $query = 'SELECT size_id FROM sizes WHERE product_id = ?';
    $result = $this->db->fetchCol($query, $productId);
    $sizes = array();
    foreach($result as $sizeId ) {
      $sizes[] = $sizeId;
    }
    return $sizes;
  }
  
  public function addProduct($product) 
  {
    $tags = $product['tags'];
    unset($product['tags']);
    $sizes = $product['sizes'];
    unset($product['sizes']);
    $photoName = $product['photo'];
    unset($product['photo']);
    $this->db->insert('products', $product);
    $productId = $this->db->lastInsertId();
    foreach($tags as $tagId) {
      $this->addTag($tagId, $productId);
    }
    foreach($sizes as $sizeId) {
      $this->addSize($sizeId, $productId);
    }
    if($photoName) {
      $this->addPhoto($productId, $photoName);
      $this->createThumbnail($photoName);
    }
    $this->invalidateProductsCache();
    
  }
  
  private function addTag($tagId, $productId)
  {
    $data = array(
      'tag_id' => $tagId,
      'product_id' => $productId
    );
    $this->db->insert('tags', $data);
  }
  
  private function addSize($sizeId, $productId)
  {
    $data = array(
      'size_id' => $sizeId,
      'product_id' => $productId
    );
    $this->db->insert('sizes', $data);
  }
  
  public function deleteProduct($productId) 
  {
    $this->db->delete('products', 'id = ' . $this->db->quote($productId));
    $this->invalidateProductsCache();
    $this->invalidateProductCache($productId);
  }
  
  public function updateProduct($data)
  {
    $productId = $data['id'];
    $tags = $data['tags'];
    unset($data['tags']);
    $photoName = $data['photo'];
    unset($data['photo']);
    $sizes = $data['sizes'];
    unset($data['sizes']);
    if($photoName) {
      $photoId = $this->addPhoto($productId, $photoName);
      if(!$data['primary_photo_id']) {
        $data['primary_photo_id'] = $photoId;
      }
      $this->createThumbnail($photoName);
    }
    $this->db->update('products', $data, 'id = ' . $this->db->quote($productId));
    $this->db->delete('tags', 'product_id = ' . $this->db->quote($productId));
    $this->db->delete('sizes', 'product_id = ' . $this->db->quote($productId));
    foreach($tags as $tagId) {
      $this->addTag($tagId, $productId);
    }
    foreach($sizes as $sizeId) {
      $this->addSize($sizeId, $productId);
    }
    $this->invalidateProductCache($productId);
  }
  
  public function addPhoto($productId, $photoName) {
    $data = array(
      'photo_name' => $photoName,
      'product_id' => $productId
    );
    $this->db->insert('photos', $data);
    return $this->db->lastInsertId();
  }
  
  const UPLOADED_PHOTO_PATH = 'uploaded/';
  const THUMBNAILS_PATH = 'uploaded/thumbs/';
  const THUMBNAIL_WIDTH = 150;
  const THUMBNAIL_HEIGHT = 225;
  
  public function createThumbnail($photoName) {
    $srcImg=imagecreatefromjpeg(self::UPLOADED_PHOTO_PATH . $photoName);
    $srcWidth = imageSX($srcImg);
    $srcHeight = imageSY($srcImg);
    $imgRatio = $srcWidth/$srcHeight;
    $thumbnailRatio = self::THUMBNAIL_WIDTH / self::THUMBNAIL_HEIGHT ;
    if($imgRatio > thumbnailRatio) {
      $thumbWidth = self::THUMBNAIL_WIDTH;
      $scale = $thumbWidth/$srcWidth; 
      $thumbHeight = $srcHeight*$scale;
    } else {
      $thumbHeight = self::THUMBNAIL_HEIGHT;
      $scale = $thumbHeight/$srcHeight; 
      $thumbWidth = $srcWidth*$scale;
    }
    $dstImg=ImageCreateTrueColor($thumbWidth,$thumbHeight);
    imagecopyresized($dstImg,$srcImg,0,0,0,0,(int)$thumbWidth,(int)$thumbHeight,$srcWidth,$srcHeight); 
    imagejpeg($dstImg,self::THUMBNAILS_PATH . $photoName);
    imagedestroy($dstImg); 
    imagedestroy($srcImg);
  }
  
  private function getPhotos($productId) {
    $query = 'SELECT photo_id, photo_name FROM photos WHERE product_id = ?';
    $result = $this->db->fetchAll($query, $productId);
    $photos = array();
    foreach($result as $photo) {
      $photos[$photo['photo_id']] = $photo['photo_name'];
    }
    return $photos;
  }
  
  public function deletePhoto($photoId) {
    $photos = new Default_Model_Photos();
    $where = $photos->getAdapter()->quoteInto('photo_id = ?', $photoId);
    $photos->delete($where);
  }
}
?>
