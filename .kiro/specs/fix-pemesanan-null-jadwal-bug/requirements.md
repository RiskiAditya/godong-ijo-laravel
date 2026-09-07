# Requirements Document

## Introduction

This document specifies the requirements for fixing a data integrity bug in the Pemesanan (booking) system where jadwal_id can be null, causing relationship chain failures. The system currently allows null jadwal_id values for fishing bookings, but legacy bookings without jadwal_id create issues when accessing paket_wisata information through the jadwal relationship. This fix ensures all pemesanan records have valid jadwal_id values by backfilling from the jadwal->paket relationship, implementing proper error handling, and enforcing transactional integrity in webhook handlers.

## Glossary

- **Pemesanan_System**: The booking management system that processes tourism package bookings
- **Jadwal_Table**: Database table storing schedule records linking paket_wisata to specific dates
- **Paket_Wisata_Table**: Database table storing tourism package information
- **Pemesanan_Table**: Database table storing booking records with customer information
- **Migration_Script**: Database migration that adds paket_wisata_id and backfills null jadwal_id
- **BookingException**: Custom exception class for booking-related errors
- **Webhook_Handler**: Controller method processing Midtrans payment notifications
- **Transaction_Block**: Database transaction wrapper ensuring atomic operations

## Requirements

### Requirement 1: Data Backfill for Null jadwal_id

**User Story:** As a system administrator, I want all existing pemesanan records with null jadwal_id to have valid jadwal_id values, so that relationship chains work correctly without errors.

#### Acceptance Criteria

1. THE Migration_Script SHALL add a paket_wisata_id column to the Pemesanan_Table as a foreign key referencing the Paket_Wisata_Table
2. WHEN the Migration_Script executes, THE Migration_Script SHALL backfill jadwal_id for all pemesanan records where jadwal_id is null by looking up the jadwal record matching paket_wisata_id and tanggal_kunjungan
3. WHERE a matching jadwal record does not exist, THE Migration_Script SHALL create a new jadwal record with paket_id set to paket_wisata_id, tanggal set to tanggal_kunjungan, and kuota_tersedia set to the paket's default kuota
4. WHEN the Migration_Script completes, THE Migration_Script SHALL log the count of backfilled records and newly created jadwal records
5. THE Migration_Script SHALL preserve all existing data values in the Pemesanan_Table during backfill operations

### Requirement 2: Custom Exception Handling

**User Story:** As a developer, I want clear exception messages when booking operations encounter null jadwal_id, so that I can quickly identify and debug data integrity issues.

#### Acceptance Criteria

1. THE Pemesanan_System SHALL define a BookingException class extending PHP's base Exception class
2. WHEN a booking operation encounters a pemesanan record with null jadwal_id, THE Pemesanan_System SHALL throw a BookingException with the message "Booking {kode_booking} has null jadwal_id"
3. WHEN a BookingException is thrown, THE Pemesanan_System SHALL log the exception with error level including kode_booking, pemesanan_id, and stack trace
4. THE BookingException SHALL accept optional context data as an array parameter in its constructor
5. WHERE context data is provided, THE Pemesanan_System SHALL include the context data in the log entry

### Requirement 3: Webhook Transaction Safety

**User Story:** As a system operator, I want payment webhook handlers to use database transactions, so that payment status updates and quota modifications are atomic and data remains consistent during failures.

#### Acceptance Criteria

1. THE Webhook_Handler SHALL wrap all database operations in a Transaction_Block using DB::transaction
2. WHEN the Webhook_Handler processes a payment notification, THE Webhook_Handler SHALL begin a Transaction_Block before performing any database write operations
3. IF an exception occurs during webhook processing, THEN THE Webhook_Handler SHALL rollback all database changes made within the Transaction_Block
4. WHEN the Webhook_Handler successfully processes a payment notification, THE Webhook_Handler SHALL commit the Transaction_Block
5. WHERE a rollback occurs, THE Webhook_Handler SHALL log the error with the order_id, transaction_status, and exception message

### Requirement 4: Migration Validation

**User Story:** As a database administrator, I want the migration to validate data integrity after backfilling, so that I can confirm all pemesanan records have valid relationships before the system goes live.

#### Acceptance Criteria

1. WHEN the Migration_Script completes backfilling, THE Migration_Script SHALL verify that zero pemesanan records have null jadwal_id
2. IF any pemesanan records still have null jadwal_id after backfill, THEN THE Migration_Script SHALL throw an exception with details of the affected records
3. THE Migration_Script SHALL verify that all backfilled jadwal_id values reference existing records in the Jadwal_Table
4. WHEN the Migration_Script creates new jadwal records, THE Migration_Script SHALL verify that the paket_id references an existing record in the Paket_Wisata_Table
5. THE Migration_Script SHALL output a validation summary listing total pemesanan records processed, jadwal records created, and any validation failures

### Requirement 5: Post-Migration Assumption Enforcement

**User Story:** As a developer, I want to assume paket_wisata_id is always present in booking operations after migration, so that I can write cleaner code without null checks everywhere.

#### Acceptance Criteria

1. WHERE the migration has been executed, THE Pemesanan_System SHALL assume paket_wisata_id is always present in Pemesanan_Table records
2. THE Pemesanan_System SHALL use paket_wisata_id directly without null checks when creating new booking records
3. WHEN accessing paket information, THE Pemesanan_System SHALL use the direct paket_wisata relationship instead of going through jadwal->paket
4. WHERE legacy code accesses paket through jadwal->paket, THE Pemesanan_System SHALL refactor to use the direct paket_wisata relationship
5. THE Pemesanan_System SHALL document in code comments that paket_wisata_id is guaranteed non-null after migration

### Requirement 6: Error Recovery Guidance

**User Story:** As a support engineer, I want clear error messages and recovery steps when encountering booking data issues, so that I can quickly resolve customer-facing problems.

#### Acceptance Criteria

1. WHEN a BookingException is thrown, THE Pemesanan_System SHALL include a recovery_hint field in the exception context
2. WHERE a pemesanan record has null jadwal_id, THE BookingException SHALL provide recovery hint "Run migration to backfill jadwal_id from paket_wisata_id and tanggal_kunjungan"
3. WHERE a pemesanan record has null paket_wisata_id, THE BookingException SHALL provide recovery hint "Contact administrator - paket_wisata_id is required and cannot be automatically recovered"
4. THE Pemesanan_System SHALL log all BookingException instances to a dedicated booking_errors log channel
5. WHEN logging a BookingException, THE Pemesanan_System SHALL include timestamp, kode_booking, pemesanan_id, error message, and recovery_hint in the log entry
