<?php

declare(strict_types = 1);

namespace metadata\examples;

use utils\File;
use metadata\Metadata;
use metadata\MetadataCollection;

/**
* Class ExampleMetadata
* @package metadata\examples
*/
final class ExampleMetadata {
  
  # Without using Metadata 
  const OLD = [
    'nick' => 'Impera',
    'first_name' => 'Impera',
    'last_name' => 'Zim',
    'age' => '+2000',
  ];
  
  # Using metadata 
  const NEW = [
    "metadata" => "ewogICAgIm5hbWUiOiAiSW1wZXJhWmltIiwKICAgICJ4dWlkIjogIjI1MzU0NTAwMjY2NjcyNzYiLAogICAgImlkIjogIjAwMDMiCn0="
  ];

  /**
  * Example Create Metadata in your file.
  */
  public function example_create_medatada(): void {
    # Your (Config) file 
    $YourFile = new File('file.yml');
    
    # Define your Collection
    $collection = new MetadataCollection();
    $collection->setMetadata('nick', 'Impera');
    $collection->setMetadata('first_name', 'Impera');
    $collection->setMetadata('last_name', 'Zim');
    $collection->setMetadata('age', '+2000');
    
    # Define Your Collection in Metadata
    $metadata = new Metadata($YourFile);
    $metadata->set($collection);
  }
  
  /**
  * Example manage value in Metadata.
  */
  public function example_manage_medatada_value(): void {
    # Your (Config) file 
    $YourFile = new File('file.yml');
    
    # Your Metadata
    $metadata = new Metadata($YourFile);
    
    # Get values
    $metadata->get('nick'); // Impera
    $metadata->get('age'); // +2000
    
    # Set values and create new values
    $metadata->set('age', '+3000'); // new age value is +3000
    $metadata->set('power', '∞'); // new value added in Metapower(power: ∞)
    
    # You can also reset the entire Metadata by obtaining the existing MetadataCollection and modifying and defining them again or creating a new one and defining.
    
    $collection = $metadata->getCollection();
    $collection->removeMetadata('power'); // power value was removed from metadata.
    $collection->clearMetadata(); // Clear the metadata 
    
    $metadata->set($collection); // Defines the new Collection value in the metadata
  }
  
}