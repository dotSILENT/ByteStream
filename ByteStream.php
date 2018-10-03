<?php
/**
 * Very simple & basic PHP Byte Stream class useful in reading & writing data when using sockets
 * 
 * Inspired by BitStream class from RakNet networking engine, although not supporting any operations on bits
 * When communcating with sockets, data is usually written & read with primitive data types, such as 2 byte integers (shorts)
 * PHP doesn't have such data types therefore this class mimics them using the pack() and unpack() functions
 * GitHub repo: <https://github.com/dotSILENT/ByteStream>
 * 
 * @author dotSILENT <dot.silentium@gmail.com>
 */

class ByteStream
{
	/**
	 * Private variables
	 *
	 * @access private
	 */

	private $bytesUsed = 0;
	private $data;
	private $readOffset = 0;

	public function getBytesUsed()
	{ 
		return (int)$this->bytesUsed; 
	}

	/**
	 * Set the offset from which the data should be read
	 *
	 * @param integer $offset Offset (index) to be set
	 * @return void
	 */
	public function setReadOffset($offset) 
	{
		if(!is_int($offset) || $offset < 0) 
			return;
		$this->readOffset = $offset;

		if($this->bytesUsed < $this->readOffset)
			$this->readOffset = $this->bytesUsed;
	}

	/**
	 * Ignore a number of bytes for reading
	 *
	 * @param integer $bytes_count Number of bytes to ignore
	 * @return void
	 */
	public function ignoreBytes($bytes_count) 
	{
		if(!is_int($bytes_count) || $bytes_count < 0)
			return;

		$this->setReadOffset($this->readOffset + $bytes_count);
	}

	/**
	 * Returns the raw data
	 *
	 * @return string
	 */
	public function getData() 
	{
		 return $this->data; 
	}

	/**
	 * Construct an instance of ByteStream class.
	 *
	 * @param string $rawdata (optional) Seed the ByteStream with raw data
	 * @param integer $len (optional) Length of the seeded data (strlen() is used if left default)
	 */
	public function __construct($rawdata = null, $len = 0)
	{
		$this->data = $rawdata;
		$this->bytesUsed = ($len == 0) ? strlen($rawdata) : $len;
	}

	/**
	 * Create a new instance of ByteStream class from another ByteStream (copy data)
	 *
	 * @param ByteStream $bs ByteStream to copy data from
	 * @return ByteStream New instance of ByteStream class
	 */
	public static function fromByteStream(ByteStream $bs)
	{
		return new ByteStream($bs->getData(), $bs->getBytesUsed());
	}
	
	/**
	 * Write raw data to the stream
	 *
	 * @access private
	 * @param string|integer $dataToWrite Data that will get written to the stream
	 * @param string $type pack() specific data type string
	 * @param integer $len Length of the written data in bytes
	 * @return void
	 */
	private function WriteData($dataToWrite, $type, $len)
	{
		$this->data .= pack($type, $dataToWrite);
		$this->bytesUsed += $len;
	}

	/**
	 * Read raw data from the stream
	 *
	 * @access private
	 * @param string $type Type of data that will be read specific to unpack() function
	 * @param integer $len Length of data that will be read in bytes
	 * @return mixed|null Read data or null on failure
	 */
	private function ReadData($type, $len)
	{
		if($this->bytesUsed - $this->readOffset < $len)
			return null;

		$tmp = substr($this->data, $this->readOffset, $len); // extract only the part of data that we need for conversion
		$buffer = unpack($type, $tmp)[1]; // data returned from unpack() starts at index 1
		$this->readOffset += $len;
		return $buffer;
	}





	/**
	 * Write functions
	 */

	/**
	 * Write a string to the stream
	 *
	 * @param string $text String that will be written
	 * @param integer $len Length of data. Default (0) uses strlen() to determine the length
	 * @return void
	 */
	public function Write($text, $len=0)
	{
		if($len == 0)
			$len = strlen($text);
		else $array = substr($text, 0, $len);
		$this->WriteData($text, "a{$len}", $len);
	}
	
	/**
	 * Write a 4 byte (32 bits) signed integer to the stream
	 *
	 * @param integer $num Signed integer to be written
	 * @return void
	 */
	public function WriteInt($num)
	{
		$this->WriteData($num, "l", 4);
	}
	
	/**
	 * Write a 4 byte (32 bits) unsigned integer to the stream
	 *
	 * @param integer $num Unsigned integer to be written
	 * @return void
	 */
	public function WriteUInt($num)
	{
		$this->WriteData($num, "L", 4);
	}
	
	/**
	 * Write a single byte (8 bits) to the stream
	 *
	 * @param integer|string $num Byte to be written
	 * @return void
	 */
	public function WriteByte($num)
	{
		$this->WriteData($num, "C", 1);
	}
	
	/**
	 * Write a signed short (16 bits) to the stream
	 *
	 * @param integer $num Signed short to be written
	 * @return void
	 */
	public function WriteShort($num)
	{
		$this->WriteData($num, "s", 2);
	}
	
	/**
	 * Write a unsigned short (16 bits) to the stream
	 *
	 * @param integer $num Unsigned short to be written
	 * @return void
	 */
	public function WriteUShort($num)
	{
		$this->WriteData($num, "S", 2);
	}
	
	/**
	 * Write a 4 byte float (32 bits) to the stream
	 *
	 * @param integer $num Float to be written
	 * @return void
	 */
	public function WriteFloat($num)
	{
		$f = pack("f", $num);
		$this->WriteData($num, "f", strlen($f));
	}
	
	/**
	 * Read functions
	 */
	
	/**
	 * Read a single byte from the stream
	 *
	 * @return integer|null The byte that was read or null on failure
	 */
	public function ReadByte()
	{
		return $this->ReadData("C", 1);
	}

	/** 
	 * Read a signed integer (4 bytes) from the stream
	 * 
	 * @return integer|null The integer that was read or null on failure
	*/
	public function ReadInt()
	{
		return $this->ReadData("l", 4);
	}
	
	/**
	 * Read a unsigned integer (4 bytes) from the stream
	 *
	 * @return integer|null The unsigned integer that was read or null on failure
	 */
	public function ReadUInt()
	{
		return $this->ReadData("L", 4);
	}
	
	/**
	 * Read a short (2 bytes) from the stream
	 *
	 * @return integer|null The short that was read or null on failure
	 */
	public function ReadShort()
	{
		return $this->ReadData("s", 2);
	}
	
	/**
	 * Read a unsigned short (2 bytes) from the stream
	 *
	 * @return integer|null The unsigned short that was read or null on failure
	 */
	public function ReadUShort()
	{
		return $this->ReadData("S", 2);
	}
	
	/**
	 * Read a float (4 bytes) from the stream
	 *
	 * @return float|null The float that was read or null on failure
	 */
	public function ReadFloat()
	{
		return (float)$this->ReadData("f", 4);
	}
	
	/**
	 * Read a string from the stream
	 *
	 * @param integer $len Length of the string to be read
	 * @return void
	 */
	public function Read($len)
	{
		return $this->ReadData("a{$len}", $len);
	}
};
?>