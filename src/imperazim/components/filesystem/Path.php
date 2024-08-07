<?php

declare(strict_types = 1);

namespace imperazim\components\filesystem;

use ZipArchive;
use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use imperazim\components\filesystem\exception\FileSystemException;

/**
* Class Path
* @package imperazim\components\filesystem
*/
final class Path {

  /**
  * Path constructor.
  * @param string $sourceFolder The source folder for operations.
  * @param bool|null $autoGenerate Whether to generate the directory if it does not exist.
  * @throws FileSystemException If the source folder cannot be created.
  */
  public function __construct(
    private string $sourceFolder,
    private ?bool $autoGenerate = false
  ) {
    try {
      if ($autoGenerate) {
        if (!file_exists($this->getFolder())) {
          if (!mkdir($this->getFolder(), 0777, true) && !is_dir($this->getFolder())) {
            throw new FileSystemException('Failed to create the destination folder.');
          }
        } elseif (!is_dir($this->getFolder())) {
          throw new FileSystemException('Source path is not a directory.');
        }
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Gets the Source folder.
  * @return string
  */
  public function getFolder(): string {
    return $this->sourceFolder;
  }

  /**
  * Compresses a directory into a ZIP file.
  * @param string $zipFileName The name of the output ZIP file.
  * @param string $destinationFolder The destination folder for operations.
  * @return string|null The full path to the ZIP file on success, or null on failure.
  * @throws FileSystemException If an error occurs during the process.
  */
  public function zipCompress(string $zipFileName, string $destinationFolder): ?string {
    try {
      if (!file_exists($destinationFolder) && !mkdir($destinationFolder, 0777, true) && !is_dir($destinationFolder)) {
        throw new FileSystemException('Failed to create the destination folder.');
      }
      $zipFilePath = rtrim($destinationFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $zipFileName;
      if (file_exists($zipFilePath)) {
        unlink($zipFilePath);
      }
      $zip = new ZipArchive();
      if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $files = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($this->getFolder()),
          RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
          if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($this->getFolder()) + 1);
            $zip->addFile($filePath, $relativePath);
          }
        }
        $zip->close();
        return $zipFilePath;
      } else {
        throw new FileSystemException('Failed to open the ZIP file for writing.');
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
      return null;
    }
  }

  /**
  * Extracts a ZIP file into a specified directory.
  * @param string $zipFileName The name of the ZIP file to extract.
  * @param string $destinationFolder The destination folder for operations.
  * @return string|null The full path to the extracted directory on success, or null on failure.
  * @throws FileSystemException If an error occurs during the process.
  */
  public function unzipExtract(string $zipFileName, string $destinationFolder): ?string {
    try {
      $zipFilePath = rtrim($this->getFolder(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $zipFileName;
      if (!file_exists($zipFilePath)) {
        throw new FileSystemException('The ZIP file does not exist.');
      }
      if (!file_exists($destinationFolder) && !mkdir($destinationFolder, 0777, true) && !is_dir($destinationFolder)) {
        throw new FileSystemException('Failed to create the destination folder.');
      }
      $zip = new ZipArchive();
      if ($zip->open($zipFilePath) === true) {
        $zip->extractTo($destinationFolder);
        $zip->close();
        return $destinationFolder;
      } else {
        throw new FileSystemException('Failed to open the ZIP file for reading.');
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
      return null;
    }
  }

  /**
  * Copies a folder and its contents recursively to another directory.
  * @param Path|string $destinationFolder The destination folder for operations.
  * @throws FileSystemException If an error occurs during the copy process.
  */
  public function copyFolderTo(Path|string $destinationFolder): void {
    try {
      if ($destinationFolder instanceof Path) {
        $destinationFolder = $destinationFolder->getFolder();
      }
      $this->copyFolderRecursive($this->getFolder(), $destinationFolder);
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Helper method to copy a folder and its contents recursively.
  * @param string $sourceFolder The source folder to copy.
  * @param string $destinationFolder The destination folder to copy to.
  * @throws FileSystemException If an error occurs during the copy process.
  */
  private function copyFolderRecursive(string $sourceFolder, string $destinationFolder): void {
    try {
      if (!file_exists($sourceFolder) || !is_dir($sourceFolder)) {
        throw new FileSystemException('The source folder does not exist.');
      }
      if (!file_exists($destinationFolder) && !mkdir($destinationFolder, 0777, true) && !is_dir($destinationFolder)) {
        throw new FileSystemException('Failed to create the destination folder.');
      }
      $files = scandir($sourceFolder);
      if ($files === false) {
        throw new FileSystemException('Failed to retrieve the contents of the source folder.');
      }
      foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
          continue;
        }
        $sourceFilePath = $sourceFolder . DIRECTORY_SEPARATOR . $file;
        $destinationFilePath = $destinationFolder . DIRECTORY_SEPARATOR . $file;

        if (is_dir($sourceFilePath)) {
          $this->copyFolderRecursive($sourceFilePath, $destinationFilePath);
        } else {
          if (!copy($sourceFilePath, $destinationFilePath)) {
            throw new FileSystemException("Failed to copy the file: $sourceFilePath");
          }
        }
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Deletes a folder and its contents recursively.
  * @throws FileSystemException If an error occurs during the delete process.
  */
  public function deleteFolderRecursive(): void {
    try {
      $folder = $this->getFolder();
      if (!file_exists($folder) || !is_dir($folder)) {
        throw new FileSystemException('The folder does not exist or is not a directory.');
      }
      $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
      );
      foreach ($files as $fileinfo) {
        $operation = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        if (!$operation($fileinfo->getRealPath())) {
          throw new FileSystemException('Failed to delete ' . $fileinfo->getRealPath());
        }
      }
      if (!rmdir($folder)) {
        throw new FileSystemException('Failed to delete the folder ' . $folder);
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Gets information about files in the resources directory and creates Path objects.
  * @param string $folder
  * @return string[]
  */
  public static function getRecursiveFiles(string $folder): array {
    $filesInfo = [];
    try {
      $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
      );

      foreach ($files as $file) {
        if ($file->isFile()) {
          $filesInfo[] = [
            'directory' => $file->getPath(),
            'fileName' => $file->getBasename(),
            'fileType' => File::getTypeByExtension($file->getExtension()),
            'content' => File::deserializeContent($file->getExtension(), file_get_contents($file->getRealPath()))
          ];
        }
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
    return $filesInfo;
  }

}