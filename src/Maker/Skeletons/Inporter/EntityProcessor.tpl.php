<?php echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use App\Importer\Main\AbstractProcessor;

class <?php echo $className; ?> extends AbstractProcessor
{
    public function process(array $data): void
    {
        $this->metadataValidator->validateHeaders($this->headerKeys, $data);

        // TODO
    }
}
