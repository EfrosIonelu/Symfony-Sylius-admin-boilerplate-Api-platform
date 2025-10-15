<?php echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;


use App\Importer\Main\AbstractImporter;
use App\Message\<?php echo $entityNamePasscalCase; ?>\<?php echo $entityImportMessageClassName; ?>;

class <?php echo $className; ?> extends AbstractImporter
{
    public function dispatch(array $data): void
    {
        $this->messageBus->dispatch(new <?php echo $entityImportMessageClassName; ?>($data));
    }
}
