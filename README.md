# Laravel Repository

Just a simple artisan command to generate your repository pattern files with ease.

## Installation

To install this package, you just install it with composer and you are ready to go.

```bash
composer require theozebua/laravel-repository --dev
```

## Usage

To use this package, you just have to run:

```bash
php artisan repository:generate
```

And it will ask you some questions. See examples below.

### Generate A Repository Interface

Run:

```bash
php artisan repository:generate
```

Then choose `Interface` either you type the the name or the index.

```bash
  What do you want to generate?
  Interface .............................................. 0
  Repository ............................................. 1
❯ Interface
```

Then type your interface name.

```bash
  What is the name of your interface? [RepositoryInterface]
❯ RepositoryInterface
```

That's it.  

And the file will be placed in `app/Repositories/Interfaces/RepositoryInterface.php`.

> Note: This steps are for the first time you generate an interface when there are no interfaces created before. If you try to generate more interfaces afterwards, it will ask you additional questions. See example below.

### Generate A Repository Interface That Extends Another Interfaces

Run:

```bash
php artisan repository:generate
```

Then choose `Interface` either you type the the name or the index.

```bash
  What do you want to generate?
  Interface .............................................. 0
  Repository ............................................. 1
❯ Interface
```

Then type your interface name.

```bash
  What is the name of your interface? [RepositoryInterface]
❯ RepositoryInterface
```

It will ask you if you want to extends another interfaces or not. If you choose `no`, the interface will be created without extending another interfaces. But for this example we choose `yes`.

```bash
  Do you want to extends another interfaces? (yes/no) [no]
❯ yes
```

And it will ask you to choose which interfaces do you want to extend.

Then choose interfaces that you want to extend, you can either type the interface name or the index. I will type the index because the interface name is too long to type.

```bash
  Please choose interface(s) that you want to extends separated by comma:
  App\Repositories\Interfaces\AnotherInterface ..................... 0  
  App\Repositories\Interfaces\OtherInterface ....................... 1  
❯ 0,1
```

And done.

### Generate A Repository Class

Run:

```bash
php artisan repository:generate
```

Then choose `Repository` either you type the the name or the index.

```bash
  What do you want to generate?
  Interface .............................................. 0
  Repository ............................................. 1
❯ Repository
```

Then type your repository name.

```bash
  What is the name of your repository? [Repository]
❯ Repository
```

That's it.  

And the file will be placed in `app/Repositories/Implementations/Repository.php`.

> Note: Same as the interface generator, this steps are for the first time you generate a repository when there are no interfaces created before. If you try to generate more repositories afterwards, it will ask you additional questions. See example below.

### Generate A Repository Class That Implements Some Interfaces

Run:

```bash
php artisan repository:generate
```

Then choose `Repository` either you type the the name or the index.

```bash
  What do you want to generate?
  Interface .............................................. 0
  Repository ............................................. 1
❯ Repository
```

Then type your repository name.

```bash
  What is the name of your repository? [Repository]
❯ Repository
```

It will ask you if you want to implements some interfaces or not. If you choose `no`, the repository will be created without implementing any interfaces. But for this example we choose `yes`.

```bash
  Do you want to implements some interfaces? (yes/no) [no]
❯ yes
```

And it will ask you to choose which interfaces do you want to implement.

```bash
  Please choose interface(s) that you want to implements separated by comma:
  App\Repositories\Interfaces\AnotherInterface ..................... 0  
  App\Repositories\Interfaces\OtherInterface ....................... 1  
❯ 0,1
```

And done.

## Configuration

Most of the time, you don't need to configure anything. But in case you want to configure the path, you can publish the configuration file with this simple command.

```bash
php artisan vendor:publish --tag=laravel-repository-config
```