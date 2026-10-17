<?php
use PHPUnit\Framework\TestCase;

class BeneficiaryTest extends TestCase
{
    public function testIdGeneratorAndCreateBeneficiary()
    {
        $idGen = new \GOF\Services\IdGenerator();
        $seq1 = $idGen->next('beneficiary');
        $this->assertIsInt($seq1);
        $gof1 = $idGen->formatGOFId('beneficiary', $seq1);
        $this->assertStringContainsString('GOF-ORP', $gof1);

        $model = new \GOF\Models\Beneficiary();
        $data = [
            'gof_id' => $gof1,
            'full_name' => 'Test Child',
            'dob' => '2015-01-01',
            'gender' => 'female',
            'created_by' => 1
        ];
        $newId = $model->create($data);
        $this->assertIsInt($newId);

        $all = $model->all(10);
        $this->assertNotEmpty($all);
        $found = array_filter($all, fn($b) => $b['gof_id'] === $gof1);
        $this->assertCount(1, $found);
    }
}
