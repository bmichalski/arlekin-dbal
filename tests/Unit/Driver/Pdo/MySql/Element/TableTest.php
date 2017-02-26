<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Calam\Dbal\Tests\Unit\Driver\Pdo\MySql\Element;

use Calam\Dbal\Driver\Pdo\MySql\Element\Column;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnDeleteReferenceOptions;
use Calam\Dbal\Driver\Pdo\MySql\Element\ForeignKeyOnUpdateReferenceOptions;
use Calam\Dbal\Driver\Pdo\MySql\Element\Index;
use Calam\Dbal\Driver\Pdo\MySql\Element\PrimaryKey;
use Calam\Dbal\Driver\Pdo\MySql\Element\Table;
use Calam\Dbal\Tests\BaseTest;
use Calam\Dbal\Tests\Helper\CommonTestHelper;

/**
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */
class TableTest extends BaseTest
{
    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::__construct
     */
    public function testConstruct()
    {
        $table = $this->createBaseNewTable();

        $this->assertAttributeSame(null, 'name', $table);
        $this->assertAttributeSame(null, 'primaryKey', $table);
        $this->assertAttributeSame([], 'columns', $table);
        $this->assertAttributeSame([], 'foreignKeys', $table);
        $this->assertAttributeSame([], 'indexes', $table);
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getName
     * @covers Calam\Dbal\SqlBased\Element\Table::setName
     */
    public function testGetAndSetName()
    {
        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $this->createBaseNewTable(),
            'name',
            uniqid('test_name_', true)
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getPrimaryKey
     * @covers Calam\Dbal\SqlBased\Element\Table::setPrimaryKey
     */
    public function testGetAndSetPrimaryKey()
    {
        $primaryKey = $this->createBaseNewPrimaryKey();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $table,
            'primaryKey',
            $primaryKey
        );

        $this->assertSame(
            $table,
            $primaryKey->getTable()
        );

        CommonTestHelper::testBasicGetAndSetForProperty(
            $this,
            $table,
            'primaryKey',
            null
        );

        $this->assertNull(
            $primaryKey->getTable()
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getColumns
     * @covers Calam\Dbal\SqlBased\Element\Table::setColumns
     */
    public function testGetAndSetColumns()
    {
        $column = $this->createBaseNewColumn();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $table,
            'columns',
            [
                $column,
            ]
        );

        $this->assertSame(
            $table,
            $column->getTable()
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getForeignKeys
     * @covers Calam\Dbal\SqlBased\Element\Table::setForeignKeys
     */
    public function testGetAndSetForeignKeys()
    {
        $foreignKey = $this->createBaseNewForeignKey();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $table,
            'foreignKeys',
            [
                $foreignKey,
            ]
        );

        $this->assertEquals(
            $table,
            $foreignKey->getTable()
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getIndexes
     * @covers Calam\Dbal\SqlBased\Element\Table::setIndexes
     */
    public function testGetAndSetIndexes()
    {
        $index = $this->createBaseNewIndex();

        $table = $this->createBaseNewTable();

        CommonTestHelper::testBasicGetAndSetCollectionForProperty(
            $this,
            $table,
            'indexes',
            [
                $index,
            ]
        );

        $this->assertSame(
            $table,
            $index->getTable()
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::toArray
     */
    public function testToArray()
    {
        $table2 = $this->createBaseNewTable();

        $table2->setName(
            'table2'
        );

        $table2IdColumn = $this->createBaseNewColumn();

        $table2IdColumn->setName('id');

        $table2->setColumns(
            [
                $table2IdColumn,
            ]
        );

        $table = $this->createBaseNewTable();

        $columnDeptNo = $this->createBaseNewColumn();

        $columnDeptNo->setName(
            'deptNo'
        )->setDataType(
            'VARCHAR'
        )->setNullable(
            false
        )->setParameters(
            [
                'length' => 4,
            ]
        );

        $columnDeptName = $this->createBaseNewColumn();

        $columnDeptName->setName(
            'deptName'
        )->setDataType(
            'VARCHAR'
        )->setNullable(
            false
        )->setParameters(
            [
                'length' => 40,
            ]
        );

        $table2ReferencedIdColumn = $this->createBaseNewColumn();

        $table2ReferencedIdColumn->setName(
            'table2_id'
        )->setDataType(
            'INT'
        )->setNullable(
            false
        )->setParameters(
            [
                'length' => 11,
            ]
        );

        $primaryKey = $this->createBaseNewPrimaryKey();

        $primaryKey->setColumns(
            [
                $columnDeptNo,
            ]
        );

        $table->setName(
            'departments'
        )->setColumns(
            [
                $columnDeptNo,
                $columnDeptName,
                $table2ReferencedIdColumn,
            ]
        );

        $table->setPrimaryKey($primaryKey);

        $index = $this->createBaseNewIndex();

        $index->setName(
            'unique_deptName'
        )->setType(
            'UNIQUE'
        )->setColumns(
            [
                $columnDeptName,
            ]
        );

        $table->setIndexes(
            [
                $index,
            ]
        );

        $foreignKey = $this->createBaseNewForeignKey();

        $foreignKey->setColumns(
            [
                $table2ReferencedIdColumn,
            ]
        )->setReferencedColumns(
            [
                $table2IdColumn,
            ]
        )->setReferencedTable(
            $table2
        );

        $table->addForeignKey(
            $foreignKey
        );

        $expected = [
            'name' => 'departments',
            'columns' => [
                [
                    'name' => 'deptNo',
                    'dataType' => 'VARCHAR',
                    'nullable' => false,
                    'parameters' => [
                        'length' => 4,
                    ],
                    'autoIncrementable' => false,
                ],
                [
                    'name' => 'deptName',
                    'dataType' => 'VARCHAR',
                    'nullable' => false,
                    'parameters' => [
                        'length' => 40,
                    ],
                    'autoIncrementable' => false,
                ],
                [
                    'name' => 'table2_id',
                    'dataType' => 'INT',
                    'nullable' => false,
                    'parameters' => [
                        'length' => 11,
                    ],
                    'autoIncrementable' => false,
                ],
            ],
            'primaryKey' => [
                'columns' => [
                    'deptNo',
                ]
            ],
            'indexes' => [
                [
                    'name' => 'unique_deptName',
                    'kind' => 'UNIQUE',
                    'columns' => [
                        'deptName',
                    ],
                ],
            ],
            'foreignKeys' => [
                [
                    'columns' => [
                        'table2_id',
                    ],
                    'referencedTable' => 'table2',
                    'referencedColumns' => [
                        'id',
                    ],
                    'onDelete' => ForeignKeyOnDeleteReferenceOptions::ON_DELETE_RESTRICT,
                    'onUpdate' => ForeignKeyOnUpdateReferenceOptions::ON_UPDATE_RESTRICT,
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            $table->toArray()
        );

        $table->setPrimaryKey(null);

        $expected['primaryKey'] = null;

        $this->assertEquals(
            $expected,
            $table->toArray()
        );
    }
    
    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::hasForeignKeyWithColumnsAndReferencedColumnsNamed
     */
    public function testHasForeignKeyWithColumnsAndReferencedColumnsNamed()
    {
        $testTable = new Table();

        $testTable->setName('test');

        $this->assertFalse(
            $testTable->hasForeignKeyWithColumnsAndReferencedColumnsNamed(
                [
                    'testColumnInTest',
                ],
                'test2',
                [
                    'testColumnInTest2',
                ]
            )
        );

        $testTable2 = new Table();

        $testTable2->setName('test2');

        $testColumnInTest = new Column();

        $testColumnInTest->setName('testColumnInTest');

        $testColumnInTest2 = new Column();

        $testColumnInTest2->setName('testColumnInTest2');

        $testTable->setColumns(
            [
                $testColumnInTest,
            ]
        );

        $testTable2->setColumns(
            [
                $testColumnInTest2,
            ]
        );

        $testForeignKey = new ForeignKey();

        $testForeignKey->setColumns(
            [
                $testColumnInTest,
            ]
        );

        $testForeignKey->setReferencedColumns(
            [
                $testColumnInTest2,
            ]
        );

        $testForeignKey->setReferencedTable($testTable2);

        $testTable->setForeignKeys(
            [
                $testForeignKey,
            ]
        );

        $this->assertTrue(
            $testTable->hasForeignKeyWithColumnsAndReferencedColumnsNamed(
                [
                    'testColumnInTest',
                ],
                'test2',
                [
                    'testColumnInTest2',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::hasColumn
     */
    public function testHasColumn()
    {
        $table = new Table();

        $column = new Column();

        $this->assertFalse(
            $table->hasColumn($column)
        );

        $table->setColumns(
            [
                $column,
            ]
        );

        $this->assertTrue(
            $table->hasColumn($column)
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::hasColumnWithName
     */
    public function testHasColumnWithName()
    {
        $table = new Table();

        $column = new Column();

        $column->setName('test');

        $this->assertFalse(
            $table->hasColumnWithName('test')
        );

        $table->setColumns(
            [
                $column,
            ]
        );

        $this->assertTrue(
            $table->hasColumnWithName('test')
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::hasIndexWithName
     */
    public function testHasIndexWithName()
    {
        $table = new Table();

        $index = new Index();

        $index->setName('test');

        $this->assertFalse(
            $table->hasIndexWithName('test')
        );

        $table->setIndexes(
            [
                $index,
            ]
        );

        $this->assertTrue(
            $table->hasIndexWithName('test')
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::hasPrimaryKeyWithColumnsNamed
     */
    public function testHasPrimaryKeyWithColumnsNamed()
    {
        $table = new Table();

        $column = new Column();
        $column2 = new Column();

        $column->setName('test');
        $column2->setName('test2');

        $primaryKey = new PrimaryKey();

        $primaryKey->setColumns(
            [
                $column,
                $column2,
            ]
        );

        $this->assertFalse(
            $table->hasPrimaryKeyWithColumnsNamed(
                [
                    'test',
                    'test2',
                ]
            )
        );

        $table->setPrimaryKey($primaryKey);

        $this->assertTrue(
            $table->hasPrimaryKeyWithColumnsNamed(
                [
                    'test',
                    'test2',
                ]
            )
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getIndexWithName
     */
    public function testGetIndexWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        $index = new Index();

        $index->setName('test');

        $table->setIndexes(
            [
                $index,
            ]
        );

        $result = $table->getIndexWithName('test');

        $this->assertSame($index, $result);
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getIndexWithName
     */
    public function testGetIndexWithNameExceptionIfTableHasNoIndexWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        CommonTestHelper::assertExceptionThrown(
            function () use ($table) {
                $table->getIndexWithName('test');
            },
            \Exception::class,
            'Table "testTable" has no index with name "test".'
        );
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getColumnWithName
     */
    public function testGetColumnWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        $column = new Column();

        $column->setName('test');

        $table->setColumns(
            [
                $column,
            ]
        );

        $result = $table->getColumnWithName('test');

        $this->assertSame($column, $result);
    }

    /**
     * @covers Calam\Dbal\SqlBased\Element\Table::getColumnWithName
     */
    public function testGetColumnWithNameExceptionIfTableHasNoColumnWithName()
    {
        $table = new Table();

        $table->setName('testTable');

        CommonTestHelper::assertExceptionThrown(
            function () use ($table) {
                $table->getColumnWithName('test');
            },
            \Exception::class,
            'Table "testTable" has no column with name "test".'
        );
    }

    /**
     * @return Column
     */
    protected function createBaseNewColumn()
    {
        return $this->getMockForAbstractClass(
            Column::class
        );
    }

    /**
     * @return Index
     */
    protected function createBaseNewIndex()
    {
        return $this->getMockForAbstractClass(
            Index::class
        );
    }

    /**
     * @return ForeignKey
     */
    protected function createBaseNewForeignKey()
    {
        return $this->getMockForAbstractClass(
            ForeignKey::class
        );
    }

    /**
     * @return PrimaryKey
     */
    protected function createBaseNewPrimaryKey()
    {
        return $this->getMockForAbstractClass(
            PrimaryKey::class
        );
    }

    /**
     * @return Table
     */
    protected function createBaseNewTable()
    {
        return $this->getMockForAbstractClass(
            Table::class
        );
    }
}
