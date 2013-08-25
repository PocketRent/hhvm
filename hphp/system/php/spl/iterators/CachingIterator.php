<?php

// This doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/class.cachingiterator.php )
 *
 * This object supports cached iteration over another iterator.
 *
 */
class CachingIterator
  extends IteratorIterator
  implements OuterIterator, ArrayAccess, Countable {

  private $flags;
  private $fullCacheIterator;
  private $valid;
  private $strValue;

  const CALL_TOSTRING = 1;
  const TOSTRING_USE_KEY = 2;
  const TOSTRING_USE_CURRENT = 4;
  const TOSTRING_USE_INNER = 8;
  const CATCH_GET_CHILD = 16;
  const FULL_CACHE = 256;

  function __construct($iterator, $flags = CachingIterator::CALL_TOSTRING) {
    // TODO(emil): Check that $iterator is an Iterator
    $flags = $this->validateFlags($flags);
    if ($flags & self::FULL_CACHE) {
      $this->fullCacheIterator = new ArrayIterator();
    }

    $this->flags = $flags;
    parent::__construct($iterator);
  }

  private function validateFlags($flags) {
    $flags = (int)$flags;
    $string_flags = $flags & (
      self::CALL_TOSTRING | self::TOSTRING_USE_KEY |
      self::TOSTRING_USE_CURRENT | self::TOSTRING_USE_INNER
    );

    if ((($string_flags & self::CALL_TOSTRING) &&
        ($string_flags - self::CALL_TOSTRING)) ||
        (($string_flags & self::TOSTRING_USE_KEY) &&
        ($string_flags - self::TOSTRING_USE_KEY)) ||
        (($string_flags & self::TOSTRING_USE_CURRENT) &&
        ($string_flags - self::TOSTRING_USE_CURRENT))) {
      throw new InvalidArgumentException(
        'Flags must contain only one of CALL_TOSTRING, TOSTRING_USE_KEY, '.
        'TOSTRING_USE_CURRENT, TOSTRING_USE_INNER'
      );
    }

    return $flags & 0xFFFF;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.getflags.php )
   *
   *
   * @return     mixed   Description...
   */
  public function getFlags() {
    return $this->flags;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.setflags.php )
   *
   *
   * @flags      mixed   Bitmask of the flags to set.
   *
   * @return     mixed   No value is returned.
   */
  public function setFlags($flags) {
    $flags = $this->validateFlags($flags);

    if (!($flags & self::CALL_TOSTRING) &&
        ($this->flags & self::CALL_TOSTRING)) {
      throw new InvalidArgumentException(
        'Unsetting flag CALL_TO_STRING is not possible'
      );
    }

    if (!($flags & self::TOSTRING_USE_INNER) &&
        ($this->flags & self::TOSTRING_USE_INNER)) {
      throw new InvalidArgumentException(
        'Unsetting flag TOSTRING_USE_INNER is not possible'
      );
    }

    if (($flags & self::FULL_CACHE) && !($this->flags & self::FULL_CACHE)) {
      $this->fullCacheIterator = new ArrayIterator();
    }

    $this->flags = $flags;
  }

  private function getFullCacheIterator() {
    if ($this->fullCacheIterator) {
      return $this->fullCacheIterator;
    }

    throw new BadMethodCallException(
      get_class($this).
      ' does not use a full cache (see CachingIterator::__construct)'
    );
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.rewind.php )
   *
   *
   * @return     mixed   No value is returned.
   */
  function rewind() {
    if ($this->fullCacheIterator) {
      $this->fullCacheIterator = new ArrayIterator();
    }

    parent::rewind();
    $this->moveInnerIteratorNext();
  }

  protected function _fetch($check) {
    $this->valid = parent::_fetch($check);

    if (!$check || $this->valid) {
      $current = $this->current();
      if ($this->flags & self::CALL_TOSTRING) {
        if (is_object($current)) {
          $this->strValue = $current->__toString();
        } else {
          $this->strValue = (string)$current;
        }
      } else if ($this->flags & self::TOSTRING_USE_INNER) {
        $this->strValue = $this->getInnerIterator()->__toString();
      }

      if ($this->fullCacheIterator) {
        $this->fullCacheIterator->offsetSet($this->key(), $current);
      }
    }

    return $this->valid;
  }

  private function moveInnerIteratorNext() {
    $iterator = $this->getInnerIterator();
    if ($iterator->valid()) {
      $iterator->next();
    }
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.next.php )
   *
   *
   * @return     mixed   No value is returned.
   */
  function next() {
    $this->_setPosition($this->_getPosition() + 1);
    $this->_fetch(true);
    $this->moveInnerIteratorNext();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.valid.php )
   *
   *
   * @return     mixed   Returns TRUE on success or FALSE on failure.
   */
  function valid() {
    return $this->valid;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.hasnext.php )
   *
   *
   * @return     mixed   Returns TRUE on success or FALSE on failure.
   */
  function hasNext() {
    return $this->getInnerIterator()->valid();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.count.php )
   *
   *
   * @return     mixed   The count of the elements iterated over.
   */
  function count() {
    return $this->getFullCacheIterator()->count();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.offsetexists.php
   * )
   *
   *
   * @index      mixed   The index being checked.
   *
   * @return     mixed   Returns TRUE if an entry referenced by the offset
   *                     exists, FALSE otherwise.
   */
  function offsetExists($index) {
    return $this->getFullCacheIterator()->offsetExists($index);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.offsetget.php )
   *
   *
   * @index      mixed   Description...
   *
   * @return     mixed   Description...
   */
  function offsetGet($index) {
    return $this->getFullCacheIterator()->offsetGet($index);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.offsetset.php )
   *
   *
   * @index      mixed   The index of the element to be set.
   * @newval     mixed   The new value for the index.
   *
   * @return     mixed   No value is returned.
   */
  function offsetSet($index, $newval) {
    return $this->getFullCacheIterator()->offsetSet($index, $newval);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.offsetunset.php
   * )
   *
   *
   * @index      mixed   The index of the element to be unset.
   *
   * @return     mixed   No value is returned.
   */
  function offsetUnset($index) {
    return $this->getFullCacheIterator()->offsetUnset($index);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.getcache.php )
   *
   *
   * @return     mixed   Description...
   */
  function getCache() {
    return $this->getFullCacheIterator()->getArrayCopy();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/cachingiterator.tostring.php )
   *
   *
   * @return     mixed   The string representation of the current element.
   */
  function __toString() {
    if ($this->flags & self::TOSTRING_USE_KEY) {
      return (string)$this->key();
    } else if ($this->flags & self::TOSTRING_USE_CURRENT) {
      return (string)$this->current();
    }
    if (!($this->flags & (self::CALL_TOSTRING | self::TOSTRING_USE_INNER))) {
      throw new Exception(
        'CachingIterator does not fetch string value '.
        '(see CachingIterator::__construct)'
      );
    }
    return $this->strValue;
  }
}
