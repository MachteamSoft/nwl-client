<?php

namespace Mach\Bundle\NwlBundle\Sender;

/**
 * @group mach
 * @author Rares Vlasceanu
 */
class FileQueueBuilderTest extends \PHPUnit\Framework\TestCase
{

    const TEST_FILE = '/tmp/phpunit-test-builder';

    public function testColumnSetter()
    {
        $builder = new FileQueueBuilder();
        $columns = array('firstname', 'lastname');

        $builder->setColumnNames($columns);
        $this->assertEquals(0, $builder->count());

        $data = $builder->getContents();
        foreach ($columns as $column) {
            $this->assertContains($column, $data);
        }

        return $builder;
    }

    /**
     * @depends testColumnSetter
     * @param FileQueueBuilder $builder
     * @return \Mach\Bundle\NwlBundle\Sender\FileQueueBuilder
     */
    public function testAddingRows(FileQueueBuilder $builder)
    {
        $users = array(
            array('firstname' => 'John', 'lastname' => 'Doe'),
            array('firstname' => 'Test', 'lastname' => 'User'),
            array('firstname' => 'Ion Ionescu', 'lastname' => 'Master <"test">'),
            array('firstname' => 'Mitica', 'lastname' => 'User 100% |Tare|')
        );

        foreach ($users as $user) {
            $builder->addRow($user);
        }
        $this->assertEquals(sizeof($users), $builder->count());

        $enclosure = preg_quote(FileQueueBuilder::DATA_ENCLOSURE);
        $delim = preg_quote(FileQueueBuilder::RECORD_DELIMITER);

        $data = $builder->getContents();
        foreach ($users as $user) {
            array_walk($user, 'preg_quote');
            $pattern = '/' . implode("[,{$enclosure}]{1,3}", $user)
                    . "[{$enclosure}{$delim}$]{1,2}/";

            $this->assertRegExp($pattern, $data);
        }

        return $builder;
    }

    /**
     * @depends testAddingRows
     * @param FileQueueBuilder $builder
     */
    public function testColumnChange(FileQueueBuilder $builder)
    {
        try {
            $builder->setColumnNames(array('userid', 'username'));
            $this->fail('RuntimeException expected, but not thrown!');
        } catch (\Exception $e) {
            $this->assertInstanceOf('RuntimeException', $e);
        }
    }

    /**
     * @depends testAddingRows
     * @param FileQueueBuilder $builder
     */
    public function testSave(FileQueueBuilder $builder)
    {
        $data = $builder->getContents();
        $this->assertNotEmpty($data);

        $builder->save(self::TEST_FILE);
        $fileData = file_get_contents(self::TEST_FILE);
        $this->assertEquals($data, $fileData);

        if (is_file(self::TEST_FILE)) {
            unlink(self::TEST_FILE);
        }
    }

    /**
     * Test workarounds for string enclosure breaking bug
     * (if last char in a string is an unescaped "\" or [new line])
     */
    public function testFilePutCsvFixes()
    {
        $gen = new FileQueueBuilder();
        $gen->setColumnNames(array('id', 'name'));

        // new lines at the end of the string
        foreach (array("\n", "\r", "\r\n") as $newline) {
            $gen->reset();
            $gen->addRow(array('id' => 1, 'name' => "Popescu{$newline}"));
            $result = $gen->getContents();
            $this->assertNotContains("Popescu{$newline}", $result);
            $this->assertContains("Popescu", $result);
        }

        // new lines in the string
        foreach (array("\n", "\r", "\r\n") as $newline) {
            $gen->reset();
            $gen->addRow(array('id' => 1, 'name' => "Popescu{$newline}Ion"));
            $result = $gen->getContents();
            if ($newline !== "\n") {
                $this->assertNotContains("Popescu{$newline}Ion", $result);
            }
            $this->assertContains("Popescu\nIon", $result);
        }
        
        // non-escaped backslash at the end on string
        $gen->reset();
        $gen->addRow(array('id' => 1, 'name' => "test\\"));
        $this->assertContains("test\\\\", $gen->getContents()); // one backslash added

        // check that other backslashes are left
        $gen->reset();
        $gen->addRow(array('id' => 1, 'name' => "t\\est"));
        $this->assertNotContains("t\\\\est", $gen->getContents());
        $this->assertContains("t\\est", $gen->getContents());
    }

}
