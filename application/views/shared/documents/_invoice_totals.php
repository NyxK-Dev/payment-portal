<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<tr>
    <td colspan="2" class="border-0"></td>
    <td class="text-end fw-medium text-muted border-0 pt-4">Subtotal:</td>
    <td class="text-end text-muted font-monospace border-0 pt-4">$<?= $invoice->subtotal_aggregate; ?></td>
</tr>
<tr>
    <td colspan="2" class="border-0"></td>
    <td class="text-end fw-bold text-dark border-0 align-middle">Total Balance Due:</td>
    <td class="text-end text-primary fw-bold border-0 fs-5 font-monospace">$<?= $invoice->formatted_total_due; ?></td>
</tr>
</tbody>
</table>
</div>