<?php

namespace Provider\IO;

class File implements IFile {
  private readonly string $directoryPath;
  private readonly string $filePath;

  function __construct(string $directoryPath, string $filePath = '') {
    $directoryPath = trim($directoryPath);
    $filePath = trim($filePath);

    $directoryPath = str_remove_preg('/[\/\\\\]+$/', $directoryPath);
    $filePath = str_remove_preg('/^[\/\\\\]+/', $filePath);

    $this->directoryPath = path_normalize($directoryPath);
    $this->filePath = path_normalize($filePath);
  }

  function write(string $data) {
    $path = $this->getFullPath();

    if (!is_dir($this->getDirectoryPath())) {
      mkdir($this->getDirectoryPath(), 0777, true);
    }

    file_put_contents($path, $data);
  }

  function read(): ?string {
    $content = file_get_contents($this->getFullPath());

    if ($content === false)
      return null;

    return $content;
  }

  function delete() {
    return unlink($this->getFullPath());
  }

  function copyTo(string $path) {
    return copy($this->getFullPath(), $path);
  }

  function getSize() {
    return filesize($this->getFullPath());
  }

  function getType() {
    return filetype($this->getFullPath());
  }

  function isFile() {
    return is_file($this->getFullPath());
  }

  function isDirectory() {
    return is_dir($this->directoryPath);
  }

  function getFullPath() {
    return path_join($this->directoryPath, $this->filePath);
  }

  function getFilePath() {
    return $this->filePath;
  }

  function getDirectoryPath() {
    if ($this->isDirectory())
      return $this->filePath;

    return dirname($this->getFullPath());
  }
}
