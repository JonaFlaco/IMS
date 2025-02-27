<?php

namespace Tests\Unit;

use App\Core\Common\CTypeFieldHelper;
use App\Models\CTypeFields\CTypeField;
use PHPUnit\Framework\TestCase;

class CTypeFieldHelperTest extends TestCase
{

    // public function test_getButtons_InputEmpty_OutputZero(): void
    // {
    //     $fields = [];
    //     $result = CTypeFieldHelper::getButtons($fields);

    //     $this->assertEquals(0, sizeof($result));

    // }


    public function test_getButtons_InputListOfFields_OutputZero(): void
    {
        $fields = [
            $this->createMockField(1),
            $this->createMockField(2),
            $this->createMockField(2),
            $this->createMockField(4),
            $this->createMockField(9)
        ];

        $result = CTypeFieldHelper::getButtons($fields);

        $this->assertEquals(0, sizeof($result));

    }

    public function test_getButtons_InputListOfFields_OutputTwoButton(): void
    {
        $fields = [
            $this->createMockField(1),
            $this->createMockField(2),
            $this->createMockField(10),
            $this->createMockField(10),
            $this->createMockField(9)
        ];

        $result = CTypeFieldHelper::getButtons($fields);

        $this->assertEquals(2, sizeof($result));

    }

    private function createMockField(int $fieldTypeId) {

        $item = $this->createMock(CTypeField::class);
        $item->expects($this->once())->method("getFieldTypeId")->willReturn($fieldTypeId);

        return $item;
    }


    public function test_getUniqueTabs_InputEmpty_OutputZero(): void
    {
        $fields = [];
        $result = CTypeFieldHelper::getUniqueTabs($fields);

        $this->assertEquals(0, sizeof($result));

    }

    public function test_getUniqueTabs_InputList_OutputOne(): void
    {
        $fields = [
            $this->createMockFieldWithTabName("General"),
            $this->createMockFieldWithTabName("General"),
            $this->createMockFieldWithTabName("General"),
            $this->createMockFieldWithTabName("General"),
            $this->createMockFieldWithTabName("General"),
        ];

        $result = CTypeFieldHelper::getUniqueTabs($fields);

        $this->assertEquals(1, sizeof($result));

    }

    public function test_getUniqueTabs_InputList_OutputThree(): void
    {
        $fields = [
            $this->createMockFieldWithTabName("General"),
            $this->createMockFieldWithTabName("Personal Info"),
            $this->createMockFieldWithTabName("Personal Info"),
            $this->createMockFieldWithTabName("Payment"),
            $this->createMockFieldWithTabName("Payment"),
        ];

        $result = CTypeFieldHelper::getUniqueTabs($fields);

        $this->assertEquals(3, sizeof($result));

    }

    private function createMockFieldWithTabName(string $tabName) {

        $item = $this->createMock(CTypeField::class);
        $item->expects($this->once())->method("getTabName")->willReturn($tabName);

        return $item;
    }





    public function test_getUniqueGroupsInTab_InputEmpty_OutputZero(): void
    {
        $searchForTab = "Tab1";

        $fields = [];
        $result = CTypeFieldHelper::getUniqueGroupsInTab($fields, $searchForTab);

        $this->assertEquals(0, sizeof($result));

    }

    public function test_getUniqueGroupsInTab_InputList_OutputOne(): void
    {
        $searchForTab = "Tab1";

        $fields = [
            $this->createMockFieldWithGroupInTab("Tab1", "Group1"),
            $this->createMockFieldWithGroupInTab("Tab1", "Group1"),
            $this->createMockFieldWithGroupInTab("Tab1", "Group1"),
            $this->createMockFieldWithGroupInTab("Tab2", "Group2"),
            $this->createMockFieldWithGroupInTab("Tab2", "Group2"),
        ];

        $result = CTypeFieldHelper::getUniqueGroupsInTab($fields, $searchForTab);

        $this->assertEquals(1, sizeof($result));

    }

    public function test_getUniqueGroupsInTab_InputList_OutputThree(): void
    {
        $searchForTab = "Tab1";
        $fields = [
            $this->createMockFieldWithGroupInTab("Tab1", "Group1"),
            $this->createMockFieldWithGroupInTab("Tab1", "Group1"),
            $this->createMockFieldWithGroupInTab("Tab1", "Group2"),
            $this->createMockFieldWithGroupInTab("Tab1", "Group3"),
            $this->createMockFieldWithGroupInTab("Tab2", "Group4"),
        ];

        $result = CTypeFieldHelper::getUniqueGroupsInTab($fields, $searchForTab);

        $this->assertEquals(3, sizeof($result));

    }



    private function createMockFieldWithGroupInTab(string $tabName, string $groupName) {

        $item = $this->createMock(CTypeField::class);
        $item->expects($this->once())->method("getTabName")->willReturn($tabName);
        $item->expects($this->any())->method("getGroupName")->willReturn($groupName);

        return $item;
    }



    public function test_getFieldsInTabAndGroup_InputEmpty_OutputZero(): void
    {
        $tabName = "Tab1";
        $groupName = "Group1";

        $fields = [];
        $result = CTypeFieldHelper::getFieldsInTabAndGroup($fields, $tabName, $groupName);

        $this->assertEquals(0, sizeof($result));

    }

    public function test_getFieldsInTabAndGroup_InputList_OutputOne(): void
    {
        $tabName = "Tab1";
        $groupName = "Group1";

        $fields = [
            $this->createMockFieldWithTabAndGroupName("Tab1", "Group1"),
            $this->createMockFieldWithTabAndGroupName("Tab1", "Group2"),
            $this->createMockFieldWithTabAndGroupName("Tab1", "Group2"),
            $this->createMockFieldWithTabAndGroupName("Tab2", "Group3"),
            $this->createMockFieldWithTabAndGroupName("Tab2", "Group3"),
        ];

        $result = CTypeFieldHelper::getFieldsInTabAndGroup($fields, $tabName, $groupName);

        $this->assertEquals(1, sizeof($result));

    }

    public function test_getFieldsInTabAndGroup_InputList_OutputThree(): void
    {
        $tabName = "Tab1";
        $groupName = "Group1";

        $fields = [
            $this->createMockFieldWithTabAndGroupName("Tab1", "Group1"),
            $this->createMockFieldWithTabAndGroupName("Tab1", "Group1"),
            $this->createMockFieldWithTabAndGroupName("Tab1", "Group1"),
            $this->createMockFieldWithTabAndGroupName("Tab1", "Group2"),
            $this->createMockFieldWithTabAndGroupName("Tab2", "Group4"),
        ];

        $result = CTypeFieldHelper::getFieldsInTabAndGroup($fields, $tabName, $groupName);

        $this->assertEquals(3, sizeof($result));

    }


    private function createMockFieldWithTabAndGroupName(string $tabName, string $groupName) {

        $item = $this->createMock(CTypeField::class);
        $item->expects($this->once())->method("getTabName")->willReturn($tabName);
        $item->expects($this->any())->method("getGroupName")->willReturn($groupName);

        return $item;
    }

}
