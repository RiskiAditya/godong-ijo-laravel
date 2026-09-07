# Bugfix Requirements Document

## Introduction

The admin bookings index page crashes with a "Attempt to read property 'tanggal' on null" error when displaying bookings that have a null `jadwal` relationship. This occurs because some bookings in the database have null `jadwal_id`, and the view attempts to chain property access through the null relationship before the null coalescing operator can take effect. This bugfix ensures the page loads successfully for all bookings regardless of their `jadwal` relationship status.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a booking has a null `jadwal_id` AND the admin views the bookings index page THEN the system crashes with error "Attempt to read property 'tanggal' on null" at line 93

1.2 WHEN the view code `{{ $booking->jadwal->tanggal->format('d M Y') ?? 'N/A' }}` is executed on a booking with null jadwal THEN the system throws an error before evaluating the null coalescing operator

### Expected Behavior (Correct)

2.1 WHEN a booking has a null `jadwal_id` AND the admin views the bookings index page THEN the system SHALL display 'N/A' in the "Tanggal Kunjungan" column without crashing

2.2 WHEN the view attempts to display the visit date for a booking with null jadwal THEN the system SHALL safely check for null before attempting property access

2.3 WHEN a booking has a valid `jadwal` relationship with a non-null tanggal THEN the system SHALL display the formatted date (e.g., "15 Jan 2026")

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a booking has a valid `jadwal_id` with a populated tanggal field THEN the system SHALL CONTINUE TO display the formatted date in 'd M Y' format (e.g., "15 Jan 2026")

3.2 WHEN the admin applies filters (status, date range, search) on the bookings index page THEN the system SHALL CONTINUE TO filter bookings correctly

3.3 WHEN the admin views other booking columns (kode booking, nama, email, status, total) THEN the system SHALL CONTINUE TO display them correctly

3.4 WHEN the admin clicks on view or delete actions THEN the system SHALL CONTINUE TO perform those actions correctly
