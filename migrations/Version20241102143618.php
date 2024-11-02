<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102143618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UQ__Courses__FC00E00030D31EDF ON Courses');
        $this->addSql('DROP INDEX UQ__Professo__A9D105340AB1B166 ON Professors');
        $this->addSql('DROP INDEX IDX_B3CA5E2DAF7ECA ON Schedules');
        $this->addSql('ALTER TABLE Schedules ALTER COLUMN StartTime NVARCHAR(255)');
        $this->addSql('ALTER TABLE Schedules ALTER COLUMN EndTime NVARCHAR(255)');
        $this->addSql('ALTER TABLE Schedules ALTER COLUMN Location NVARCHAR(50)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B3CA5E2DAF7ECA ON Schedules (CourseID) WHERE CourseID IS NOT NULL');
        $this->addSql('DROP INDEX UQ__Students__A9D10534FB7D0263 ON Students');
        $this->addSql('DROP INDEX [primary] ON CourseSelections');
        $this->addSql('ALTER TABLE CourseSelections DROP COLUMN SelectionID');
        $this->addSql('ALTER TABLE CourseSelections DROP COLUMN SelectionDate');
        $this->addSql('ALTER TABLE CourseSelections DROP COLUMN Status');
        $this->addSql('ALTER TABLE CourseSelections ALTER COLUMN StudentID INT NOT NULL');
        $this->addSql('ALTER TABLE CourseSelections ALTER COLUMN CourseID INT NOT NULL');
        $this->addSql('ALTER TABLE CourseSelections ADD PRIMARY KEY (StudentID, CourseID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UQ__Students__A9D10534FB7D0263 ON Students (Email) WHERE Email IS NOT NULL');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UQ__Professo__A9D105340AB1B166 ON Professors (Email) WHERE Email IS NOT NULL');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UQ__Courses__FC00E00030D31EDF ON Courses (CourseCode) WHERE CourseCode IS NOT NULL');
        $this->addSql('DROP INDEX PK__CourseSe__7F17912F2CA478A6 ON CourseSelections');
        $this->addSql('ALTER TABLE CourseSelections ADD SelectionID INT IDENTITY NOT NULL');
        $this->addSql('ALTER TABLE CourseSelections ADD SelectionDate DATE');
        $this->addSql('ALTER TABLE CourseSelections ADD Status NVARCHAR(20)');
        $this->addSql('ALTER TABLE CourseSelections ALTER COLUMN StudentID INT');
        $this->addSql('ALTER TABLE CourseSelections ALTER COLUMN CourseID INT');
        $this->addSql('ALTER TABLE CourseSelections ADD PRIMARY KEY (SelectionID)');
        $this->addSql('DROP INDEX UNIQ_B3CA5E2DAF7ECA ON Schedules');
        $this->addSql('ALTER TABLE Schedules ALTER COLUMN StartTime TIME(0)');
        $this->addSql('ALTER TABLE Schedules ALTER COLUMN EndTime TIME(0)');
        $this->addSql('ALTER TABLE Schedules ALTER COLUMN Location NVARCHAR(100)');
        $this->addSql('CREATE INDEX IDX_B3CA5E2DAF7ECA ON Schedules (CourseID)');
    }
}
