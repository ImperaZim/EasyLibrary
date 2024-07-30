# Changelog and Usage Guide for UI Classes

## Overview

This guide provides instructions on how to use the `Form`, `Menu`, and `Dialogue` classes from the `imperazim\components\ui` namespace. All three classes function similarly and require creating a subclass to implement their specific structure and validation logic.

## General Usage

To use any of these UI classes (`Form`, `Menu`, `Dialogue`), follow these steps:

1. **Create a Subclass**: Define a new class that extends one of the base UI classes (`Form`, `Menu`, `Dialogue`).
2. **Implement Structure Method**: Implement the `structure` method to define the UI's layout and functionality.
3. **Create an Instance**: Instantiate your subclass with a `Player` object and optional data.

### Steps to Implement

#### 1. Create a Subclass

Define a new class that extends one of the base UI classes. This class will implement the specific structure for the UI.

#### 2. Implement the Structure Method

The `structure` method is where you define the layout and functionality of the UI. This method must return an instance of the respective UI type (`IForm`, `IMenu`, `IDialogue`).

#### 3. Create an Instance and Use It

Instantiate your subclass with a `Player` object and any optional data you need to pass. The UI will be automatically sent to the player upon instantiation.

### Summary

- **Create a Subclass**: Extend one of the UI base classes (`Form`, `Menu`, `Dialogue`).
- **Implement the Structure Method**: Define the UI's layout and functionality.
- **Instantiate and Use**: Create an instance of your subclass with a `Player` object and optional data.

By following these steps, you can easily create and manage custom UI elements in your PocketMine-MP plugins.
