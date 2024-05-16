## **New Update**
Version 1.1.0 brought some features to make your life easier, see what's new below:

- - - -
## Form Developerst
New classes for creating and modifying shape, less, metadata, and more.

#### Forms 
Form-style visual interfaces.
###### Classes:
**[Form.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/form/Form.php)** Allows the player to register instances of already made forms to call them more easily!

**[FormBase.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/form/FormBase.php)** New class that is used to create forms in a more logical way, separating forms into their own classes, thus making them more organized.

###### Examples:
**[ExampleMenuForm.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/form/forms/ExampleMenuForm.php)** See how to create Menu/Long Forms.

**[ExampleModalForm.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/form/forms/ExampleModalForm.php)** See how to create Modal Forms.

**[ExampleCustomForm.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/form/forms/ExampleCustomForm.php)** See how to create Custom Forms.


#### Menus 
Inventory-style visual interfaces.
###### Classes:
**[InvMenu.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/menu/InvMenu.php)** Allows the player to register instances of already made menus to call them more easily!

**[InvMenuBase.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/menu/InvMenuBase.php)** New class that is used to create menus in a more logical way, separating menus into their own classes, thus making them more organized.

###### Examples:
**[ExampleMenu.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/menu/menus/ExampleMenu.php)** See how to create Inventory Menu.


#### Metadata
A way to save encryption data using File as a base.
###### Classes
**[Metadata.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/metadata/Metadata.php)** This class works based on receiving a File type parameter in your construct and with this you can use functions where the class will create a new key called "metadata" in the file where it will save encrypted aMetadataCollection

**[MetadataCollection.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/metadata/MetadataCollection.php)** In some Metadata methods such as set and getAll use this class to facilitate understanding the code with set, get, remove methods, among others.

**[MetadataSerializer.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/metadata/MetadataSerializer.php)** Class used as Serializer of MetadataCollection class to encode and decode class data.

###### Examples:
**[ExampleMetadata.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/metadata/examples/ExampleMetadata.php)** See how to use the Metadata class methods.

#### Serializers
Some Serializer classes do not exist in PocketMine by default.
###### Classes
**[ItemSerializer.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/utls/ItemSerializer.php)** After the release of PocketMine 5.0.0 the Serialize and Deserialize methods were removed, so I decided to create methods to replace them here.

**[StringToLocationParse.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/utls/StringToLocationParse.php)** Simplified way of encoding and decoding instances of the Location class.

#### WorldUtils
Some functions to make it easier to modify worlds.
###### Classes
**[WorldManager.php](https://github.com/ImperaZim/EasyLibrary/blob/main/src/world/WorldManager.php)** Some methods require a huge amount of code to be used natively in PocketMine, so create some methods to make your life easier.


## Full Changelog 
https://github.com/ImperaZim/EasyLibrary/compare/1.0.0...1.1.0