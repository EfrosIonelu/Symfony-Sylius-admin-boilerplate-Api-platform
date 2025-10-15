<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251015152524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_administration_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, permissions JSON NOT NULL, UNIQUE INDEX UNIQ_8580269C5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_config (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, keyword VARCHAR(50) NOT NULL, value VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_B0679B78B03A8386 (created_by_id), INDEX IDX_B0679B78896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_entity_media (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, media_id INT NOT NULL, zone VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_D3C5822EEA9FDD75 (media_id), INDEX IDX_D3C5822E81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_languages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, locale VARCHAR(10) NOT NULL, enabled TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_media (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) NOT NULL, size INT DEFAULT NULL, mime_type VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, liip_paths JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_page (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_page_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, locale VARCHAR(4) DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_D89A72B0989D9B62 (slug), INDEX IDX_D89A72B02C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_translation (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7352D7324E645A7E (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_cms_translation_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, locale VARCHAR(4) DEFAULT NULL, value LONGTEXT NOT NULL, INDEX IDX_406D0B572C2AC5D3 (translatable_id), UNIQUE INDEX app_cms_translation_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_custom_form (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_A1B130EB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_custom_form_field (id INT AUTO_INCREMENT NOT NULL, custom_form_id INT NOT NULL, field_type ENUM(\'date\', \'select\', \'text\', \'textarea\', \'checkbox\', \'radio\') COMMENT \'(DC2Type:fieldType)\' NOT NULL COMMENT \'(DC2Type:fieldType)\', allowed_values JSON DEFAULT NULL, required TINYINT(1) NOT NULL, attributes JSON DEFAULT NULL, `order` INT DEFAULT NULL, INDEX IDX_460870AE58AFF2B0 (custom_form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_custom_form_field_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, locale VARCHAR(4) DEFAULT NULL, label VARCHAR(255) NOT NULL, INDEX IDX_3B6EF81D2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_custom_form_submission (id INT AUTO_INCREMENT NOT NULL, custom_form_id INT NOT NULL, created_by_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_99F47B0258AFF2B0 (custom_form_id), INDEX IDX_99F47B02B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_custom_form_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, locale VARCHAR(4) DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_7E9176C42C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_customer (id INT AUTO_INCREMENT NOT NULL, customer_group_id INT DEFAULT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, gender VARCHAR(1) DEFAULT \'u\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, subscribed_to_newsletter TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_89B9EEA4E7927C74 (email), UNIQUE INDEX UNIQ_89B9EEA4A0D96FBF (email_canonical), INDEX IDX_89B9EEA4D2919A68 (customer_group_id), UNIQUE INDEX UNIQ_89B9EEA4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_entries_log (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, data JSON DEFAULT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, username VARCHAR(191) DEFAULT NULL, INDEX IDX_84803E3EB03A8386 (created_by_id), INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_export_file_log (id INT AUTO_INCREMENT NOT NULL, external_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, service_registry_name VARCHAR(255) NOT NULL, additional_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_form_submission_values (id INT AUTO_INCREMENT NOT NULL, form_submission_id INT NOT NULL, field_id INT NOT NULL, value LONGTEXT DEFAULT NULL, INDEX IDX_6A9841BB422B0E0C (form_submission_id), INDEX IDX_6A9841BB443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, administration_role_id INT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, username_canonical VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, password VARCHAR(255) DEFAULT NULL, last_login DATETIME DEFAULT NULL, password_reset_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, email_verification_token VARCHAR(255) DEFAULT NULL, verified_at DATETIME DEFAULT NULL, roles JSON NOT NULL, email VARCHAR(255) DEFAULT NULL, email_canonical VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_88BDF3E96B7BA4B6 (password_reset_token), UNIQUE INDEX UNIQ_88BDF3E9C4995C67 (email_verification_token), INDEX IDX_88BDF3E9913437BF (administration_role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_user_oauth (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, provider VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, access_token TEXT DEFAULT NULL, refresh_token TEXT DEFAULT NULL, INDEX IDX_B00328E0A76ED395 (user_id), UNIQUE INDEX user_provider (user_id, provider), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_customer_group (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7FCF9B0577153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_cms_config ADD CONSTRAINT FK_B0679B78B03A8386 FOREIGN KEY (created_by_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_cms_config ADD CONSTRAINT FK_B0679B78896DBBDE FOREIGN KEY (updated_by_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_cms_entity_media ADD CONSTRAINT FK_D3C5822EEA9FDD75 FOREIGN KEY (media_id) REFERENCES app_cms_media (id)');
        $this->addSql('ALTER TABLE app_cms_entity_media ADD CONSTRAINT FK_D3C5822E81257D5D FOREIGN KEY (entity_id) REFERENCES app_cms_page (id)');
        $this->addSql('ALTER TABLE app_cms_page_translation ADD CONSTRAINT FK_D89A72B02C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_cms_page (id)');
        $this->addSql('ALTER TABLE app_cms_translation_translation ADD CONSTRAINT FK_406D0B572C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_cms_translation (id)');
        $this->addSql('ALTER TABLE app_custom_form ADD CONSTRAINT FK_A1B130EB03A8386 FOREIGN KEY (created_by_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_custom_form_field ADD CONSTRAINT FK_460870AE58AFF2B0 FOREIGN KEY (custom_form_id) REFERENCES app_custom_form (id)');
        $this->addSql('ALTER TABLE app_custom_form_field_translation ADD CONSTRAINT FK_3B6EF81D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_custom_form_field (id)');
        $this->addSql('ALTER TABLE app_custom_form_submission ADD CONSTRAINT FK_99F47B0258AFF2B0 FOREIGN KEY (custom_form_id) REFERENCES app_custom_form (id)');
        $this->addSql('ALTER TABLE app_custom_form_submission ADD CONSTRAINT FK_99F47B02B03A8386 FOREIGN KEY (created_by_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_custom_form_translation ADD CONSTRAINT FK_7E9176C42C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_custom_form (id)');
        $this->addSql('ALTER TABLE app_customer ADD CONSTRAINT FK_89B9EEA4D2919A68 FOREIGN KEY (customer_group_id) REFERENCES sylius_customer_group (id)');
        $this->addSql('ALTER TABLE app_customer ADD CONSTRAINT FK_89B9EEA4A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_entries_log ADD CONSTRAINT FK_84803E3EB03A8386 FOREIGN KEY (created_by_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_form_submission_values ADD CONSTRAINT FK_6A9841BB422B0E0C FOREIGN KEY (form_submission_id) REFERENCES app_custom_form_submission (id)');
        $this->addSql('ALTER TABLE app_form_submission_values ADD CONSTRAINT FK_6A9841BB443707B0 FOREIGN KEY (field_id) REFERENCES app_custom_form_field (id)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9913437BF FOREIGN KEY (administration_role_id) REFERENCES app_administration_role (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_user_oauth ADD CONSTRAINT FK_B00328E0A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_cms_config DROP FOREIGN KEY FK_B0679B78B03A8386');
        $this->addSql('ALTER TABLE app_cms_config DROP FOREIGN KEY FK_B0679B78896DBBDE');
        $this->addSql('ALTER TABLE app_cms_entity_media DROP FOREIGN KEY FK_D3C5822EEA9FDD75');
        $this->addSql('ALTER TABLE app_cms_entity_media DROP FOREIGN KEY FK_D3C5822E81257D5D');
        $this->addSql('ALTER TABLE app_cms_page_translation DROP FOREIGN KEY FK_D89A72B02C2AC5D3');
        $this->addSql('ALTER TABLE app_cms_translation_translation DROP FOREIGN KEY FK_406D0B572C2AC5D3');
        $this->addSql('ALTER TABLE app_custom_form DROP FOREIGN KEY FK_A1B130EB03A8386');
        $this->addSql('ALTER TABLE app_custom_form_field DROP FOREIGN KEY FK_460870AE58AFF2B0');
        $this->addSql('ALTER TABLE app_custom_form_field_translation DROP FOREIGN KEY FK_3B6EF81D2C2AC5D3');
        $this->addSql('ALTER TABLE app_custom_form_submission DROP FOREIGN KEY FK_99F47B0258AFF2B0');
        $this->addSql('ALTER TABLE app_custom_form_submission DROP FOREIGN KEY FK_99F47B02B03A8386');
        $this->addSql('ALTER TABLE app_custom_form_translation DROP FOREIGN KEY FK_7E9176C42C2AC5D3');
        $this->addSql('ALTER TABLE app_customer DROP FOREIGN KEY FK_89B9EEA4D2919A68');
        $this->addSql('ALTER TABLE app_customer DROP FOREIGN KEY FK_89B9EEA4A76ED395');
        $this->addSql('ALTER TABLE app_entries_log DROP FOREIGN KEY FK_84803E3EB03A8386');
        $this->addSql('ALTER TABLE app_form_submission_values DROP FOREIGN KEY FK_6A9841BB422B0E0C');
        $this->addSql('ALTER TABLE app_form_submission_values DROP FOREIGN KEY FK_6A9841BB443707B0');
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E9913437BF');
        $this->addSql('ALTER TABLE app_user_oauth DROP FOREIGN KEY FK_B00328E0A76ED395');
        $this->addSql('DROP TABLE app_administration_role');
        $this->addSql('DROP TABLE app_cms_config');
        $this->addSql('DROP TABLE app_cms_entity_media');
        $this->addSql('DROP TABLE app_cms_languages');
        $this->addSql('DROP TABLE app_cms_media');
        $this->addSql('DROP TABLE app_cms_page');
        $this->addSql('DROP TABLE app_cms_page_translation');
        $this->addSql('DROP TABLE app_cms_translation');
        $this->addSql('DROP TABLE app_cms_translation_translation');
        $this->addSql('DROP TABLE app_custom_form');
        $this->addSql('DROP TABLE app_custom_form_field');
        $this->addSql('DROP TABLE app_custom_form_field_translation');
        $this->addSql('DROP TABLE app_custom_form_submission');
        $this->addSql('DROP TABLE app_custom_form_translation');
        $this->addSql('DROP TABLE app_customer');
        $this->addSql('DROP TABLE app_entries_log');
        $this->addSql('DROP TABLE app_export_file_log');
        $this->addSql('DROP TABLE app_form_submission_values');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE app_user_oauth');
        $this->addSql('DROP TABLE sylius_customer_group');
    }
}
