<?php

// This doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from
 * http://php.net/manual/en/class.recursiveiteratoriterator.php )
 *
 * Can be used to iterate through recursive iterators.
 *
 */
class RecursiveIteratorIterator implements OuterIterator, Traversable {

  const LEAVES_ONLY = 0;
  const SELF_FIRST = 1;
  const CHILD_FIRST = 2;
  const CATCH_GET_CHILD = 16;

  private $iterators = array();
  private $originalIterator;
  private $mode;
  private $flags;

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursiveiteratoriterator.construct.php )
   *
   * Creates a RecursiveIteratorIterator from a RecursiveIterator.
   *
   * @iterator   mixed   The iterator being constructed from. Either a
   *                     RecursiveIterator or IteratorAggregate.
   * @mode       mixed   Optional mode. Possible values are
   *                     RecursiveIteratorIterator::LEAVES_ONLY - The
   *                     default. Lists only leaves in iteration.
   *                     RecursiveIteratorIterator::SELF_FIRST - Lists leaves
   *                     and parents in iteration with parents coming first.
   *                     RecursiveIteratorIterator::CHILD_FIRST - Lists
   *                     leaves and parents in iteration with leaves coming
   *                     first.
   * @flags      mixed   Optional flag. Possible values are
   *                     RecursiveIteratorIterator::CATCH_GET_CHILD which
   *                     will then ignore exceptions thrown in calls to
   *                     RecursiveIteratorIterator::getChildren().
   *
   * @return     mixed   No value is returned.
   */
  public function __construct($iterator,
                              $mode = RecursiveIteratorIterator::LEAVES_ONLY,
                              $flags = 0) {
    $this->iterators[] = array($iterator, 0);
    $this->originalIterator = $iterator;
    $this->mode = (int) $mode;
    $this->flags = $flags;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursiveiteratoriterator.getinneriterator.php
   * )
   *
   * Gets the current active sub iterator. Warning: This function is
   * currently not documented; only its argument list is available.
   *
   * @return     mixed   The current active sub iterator.
   */
  public function getInnerIterator() {
    $it = $this->iterators[count($this->iterators)-1][0];
    if (!$it instanceof RecursiveIterator) {
      throw new Exception(
        "inner iterator must implement RecursiveIterator"
      );
    }
    return $it;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursiveiteratoriterator.current.php )
   *
   *
   * @return     mixed   The current elements value.
   */
  public function current() {
    return $this->getInnerIterator()->current();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursiveiteratoriterator.key.php )
   *
   *
   * @return     mixed   The current key.
   */
  public function key() {
    return $this->getInnerIterator()->key();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursiveiteratoriterator.next.php )
   *
   *
   * @return     mixed   No value is returned.
   */
  public function next() {
    if ($this->isEmpty()) {
      return;
    }

    $it = $this->getInnerIterator();

    if ($this->mode == self::SELF_FIRST) {
      if ($it->hasChildren() && !$this->getInnerIteratorFlag()) {
        $this->setInnerIteratorFlag(1);
        $newit = $it->getChildren();
        $newit->rewind();
        $this->iterators[] = array($newit, 0);
      } else {
        $it->next();
        $this->setInnerIteratorFlag(0);
      }

      if ($this->valid()) {
        return;
      }
      array_pop($this->iterators);
      return $this->next();
    } else if ($this->mode == self::CHILD_FIRST ||
               $this->mode == self::LEAVES_ONLY) {
      if (!$it->valid()) {
        array_pop($this->iterators);
        return $this->next();
      } else if ($it->hasChildren()) {
        if (!$this->getInnerIteratorFlag()) {
          $this->setInnerIteratorFlag(1);
          $newit = $it->getChildren();
          $newit->rewind();
          $this->iterators[] = array($newit, 0);
          if ($this->valid()) {
            return;
          }
          return $this->next();
        } else {
          // CHILD_FIRST: 0 - drill down; 1 - visit 2 - next
          // LEAVES_ONLY: 0 - drill down; 1 - next
          if ($this->mode == self::CHILD_FIRST &&
              $this->getInnerIteratorFlag() == 1) {
              $this->setInnerIteratorFlag(2);
            return;
          }
        }
      }

      $this->setInnerIteratorFlag(0);
      $it->next();
      if ($this->valid()) {
        return;
      }
      return $this->next();
    } else {
      $this->setInnerIteratorFlag(0);
      $it->next();
    }
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursiveiteratoriterator.rewind.php )
   *
   *
   * @return     mixed   No value is returned.
   */
  public function rewind() {
    $it = $this->originalIterator;
    $this->iterators = array(array($it, 0));
    $it->rewind();

    // Make sure the first entry is valid
    if (!$this->valid()) {
      $this->next();
    }
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursiveiteratoriterator.valid.php )
   *
   *
   * @return     mixed   TRUE if the current position is valid, otherwise
   *                     FALSE
   */
  public function valid() {
    if ($this->isEmpty()) {
      return false;
    }

    $it = $this->getInnerIterator();
    if ($it->valid() &&
        $it->hasChildren() &&
        ($this->mode == self::LEAVES_ONLY ||
         ($this->mode == self::CHILD_FIRST &&
          $this->getInnerIteratorFlag() == 0))) {
      return false;
    }
    return $it->valid();
  }

  private function isEmpty() {
    return count($this->iterators) == 0;
  }

  private function getInnerIteratorFlag() {
    return $this->iterators[count($this->iterators)-1][1];
  }

  private function setInnerIteratorFlag($flag) {
    $this->iterators[count($this->iterators)-1][1] = $flag;
  }

  /**
   * Undocumented behavior but Zend does it and frameworks rely on it, so..
   */
  public function __call($func, $params) {
    return call_user_func_array(array($this->current(), $func), $params);
  }
}
