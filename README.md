# ByteStream

Very simple & basic PHP ByteStream class inspired by the BitStream class from RakNet networking engine.
Low-level networking code usually uses primitive data types such as single bytes, shorts, floats etc.
This class mimics these data types in PHP using pack() and unpack() functions.

## Examples of usage
Writing, reading & copying from another ByteStream
```php
$bs = new ByteStream();
$bs->WriteByte(10); // write the length of the text since we need to know the length before reading
$bs->Write('SomeText10', 10);

// Creating a new instance of ByteStream from an existing ByteStream
// We could also just read directly from $bs
$readbs = ByteStream::fromByteStream($bs);
$len = $readbs->ReadByte(); // 10
$text = $readbs->Read($len); // 'SomeText10'
```

Ignoring data, seeding with raw data, getting raw data from ByteStream
```php
$bs = new ByteStream();
$bs->Write('HEADER');
$bs->WriteShort(2222);

$readbs = new ByteStream($bs->getData(), $bs->getBytesUsed());
$readbs->ignoreBytes(6); // Ignore 'HEADER'
$num = $readbs->ReadShort(); // 2222
```

## Functions
* `getBytesUsed()`
  * Gets number of bytes currently used by the data
* `setReadOffset($offset)`
  * Sets a read offset on the data to a specific index, similar to `ignoreBytes()`
* `ignoreBytes($bytes_count)`
  * Ignores a number of bytes for reading by moving the read offset
* `getData()`
  * Gets a copy of the raw data used in ByteStream
* `fromByteStream(ByteStream $bs)`
  * Static function which returns a new instance of ByteStream with data copied from `$bs`

### Writing
* `Write($text, $len=0)`
  * Write text to the stream
* `WriteInt($num)`
  * Write a 4 byte integer
* `WriteUInt($num)`
  * Write a 4 byte unsigned integer
* `WriteByte($num)`
  * Write a single byte
* `WriteShort($num)`
  * Write a 2  byte short
* `WriteUShort($num)`
  * Write a 2 byte unsigned short
* `WriteFloat($num)`
  * Write a 4 byte float

### Reading
* `ReadByte()`
  * Read a single byte
* `ReadInt()`
  * Read a 4 byte integer
* `ReadUInt()`
  * Read a 4 byte unsigned integer
* `ReadShort()`
  * Read a 2 byte short
* `ReadUShort()`
  * Read a 2 byte unsigned short
* `ReadFloat()`
  * Read a 4 byte float
* `Read($len)`
  * Read text of given length