# TODO: Pembayaran Card & Status Updates - BLACKBOXAI Plan

Current Working Directory: c:/xampp/htdocs/ppl - Copy (2)/nora2.0

## Approved Plan Summary
- Modal for tambah pembayaran (was inline).
- Dropdown riwayat enhancement.
- Status behavior 7 (batal) available.
- Colors: behavior 3 (perbaikan/important: orange), 7 (batal: red).
- Negative input: confirm modal; positive: direct save + success modal.
- Realtime calc: Dibayar/Sisa with red if exceeds/equals.
- Files: primarily `resources/views/dashboard/registrasi_detail.php`.

Status: [ ] Not Started

## Step-by-Step Breakdown

### Step 1: [ ] Create TODO.md
- ✅ DONE: This file created.

### Step 2: [ ] Backup current files
```
cp resources/views/dashboard/registrasi_detail.php resources/views/dashboard/registrasi_detail.php.backup
```

### Step 3: [ ] Update registrasi_detail.php - Modal & Form
- Convert inline payment form to modal (structure like editModal).
- Add inputs: nominal (remove min=1, allow negative), tanggal, catatan.
- Button: 'Tambah Pembayaran' → opens modal.
- Realtime calc JS below nominal: copy from registrasi_create.php, red if bayar >= total.

### Step 4: [ ] Update JS - Realtime & Validation
- Add input listeners for realtime 'Dibayar/Sisa'.
- submitBayar(): 
  - If nominal < 0: show confirm modal ('Yakin input negatif?').
  - Else: save → success modal like status update.
- Close modal after success.

### Step 5: [ ] Fix Status Dropdown Logic
- Update PHP dropdown: always show behavior 7 (batal) if canBeCancelled().
- Ensure behaviors 0,1 → next + 3(perbaikan),7(batal).
- Fix current 0/1 missing next options.

### Step 6: [ ] Update Colors by behavior_role
```
behavior 3 (perbaikan): bg=#fff3e0, color=#f57c00 (orange/important)
behavior 7 (batal): bg=#ffebee, color=#c62828 (red)
```
- Add to $role switch in detail.php header & list.php.

### Step 7: [ ] Enhance Riwayat Dropdown/Card
- Make riwayat table collapsible/accordion in payment card.
- Add 'Lihat Semua' link if >5 entries.

### Step 8: [ ] Test Changes
- Test: open payment card → modal → input positive/negative → confirm/save → realtime → modals.
- Test status dropdown: from behavior 0/1 → see 3,7 options.
- Test colors: verify 3 orange, 7 red.
- Check riwayat shows new entries.

### Step 9: [ ] Backend Check (if needed)
- Verify gate=transaksi_store handles negative OK (already does).

### Step 10: [ ] Complete & Clean
- Remove TODO.md or mark ✅.
- Test full flow.

**Next Action:** Edit `resources/views/dashboard/registrasi_detail.php` (Steps 3-6 together via edit_file).

Progress: 5/10

**Updated:**
- ✅ Step 3: Payment modal HTML added (Tambah button → modal).
- ✅ Step 4: Modal functions (open/close), riwayat toggle renamed, success/negative modals.
- ✅ Step 5: Realtime calc JS + modal submit with negative confirm + success modal.

