# <h3 align="center">EasyLibrary 1.0.0</h3> 
![EasyLibrary](https://raw.githubusercontent.com/ImperaZim/EasyLibrary/icon.png)
_A plugin for you to define groups and roles for players and permissions for these roles easily!_
### v2.0.0 Features
- **Code Update**
  - *New data saving options*
  - *Complete refactoring of source code*
- **New functions**
  - *User Permission*
  - *Update Group Definitions*
  - *Setting groups on timed players*
- - - -
## Compatibility 
This plugin is meant to be used on servers made only in the software **[PocketMine-MP](https://github.com/pmmp/PocketMine-MP)**, it may not work perfectly in variants of it.

## Main Command
| Usage |   Description   |
| :---: | :---------: |
| /group [args...] | Default Command |

### Command Args
| Usage | Description |
| :-----: | :---------: | 
| help | See list of subcommands |
| create | Create a new groups | 
| delete | Delete a group exists |
| update | Update a group's settings |
|  set   | Update a player's group |
| setdefault | Update the server's default group |
| permission | Edit a group or player's permissions |

### Commands Permissions
| Command | Permission | Default |
| ------- | ---------- | ------- |
| all | `easygroups.command` | `OPERATOR` |
| help | `easygroups.command.help` | `OPERATOR` |
| create | `easygroups.command.create` | `OPERATOR` |
| delete | `easygroups.command.delete` | `OPERATOR` |
| update | `easygroups.command.update` | `OPERATOR` |
| set | `easygroups.command.group` | `OPERATOR` |
| setdefault | `easygroups.command.setdefault` | `OPERATOR` |
| permission | `easygroups.command.permission` | `OPERATOR` |

## License
```
© ImperaZim • EasyGroups 2022 - 2023
EasyGroups, the group plugin with many features for PocketMine-MP

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
```