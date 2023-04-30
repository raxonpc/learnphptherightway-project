<!DOCTYPE html>
<html>

<head>
    <title>Transactions</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        table tr th,
        table tr td {
            padding: 5px;
            border: 1px #eee solid;
        }

        tfoot tr th,
        tfoot tr td {
            font-size: 20px;
        }

        tfoot tr th {
            text-align: right;
        }

        .negative {
            color: red;
        }

        .positive {
            color: green;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Check #</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require APP_PATH . "App.php";
            $transactions = get_transactions(FILES_PATH);
            if ($transactions === false) {
                return;
            }
            $total_income = 0;
            $total_expense = 0;
            foreach (array_slice($transactions, 1) as $transaction) {
                echo "<tr>\n";

                [$date, $check, $description, $amount] = $transaction;

                $formatted_date = date('M j, Y', strtotime($date));
                echo "<td>$formatted_date</td>\n";
                echo "<td>$check</td>\n";
                echo "<td>$description</td>\n";

                // check if amount is negative
                $negative = $amount[0] === "-" ? true : false;

                // sometimes the value has ',' as a separator
                $amount = str_replace(",", "", $amount);
                if ($negative === true) {
                    $value = floatval(substr($amount, 2));
                    $total_expense += $value;
                    $classname = "negative";
                } else {
                    $value = floatval(substr($amount, 1));
                    $total_income += $value;
                    $classname = "positive";
                }

                echo "<td class=\"$classname\">$amount</td>\n";
                echo "</tr>\n";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total Income:</th>
                <td>
                    <?php echo "$" . number_format($total_income, 2, '.', ''); ?>
                </td>
            </tr>
            <tr>
                <th colspan="3">Total Expense:</th>
                <td>
                    <?php echo "-$" . number_format($total_expense, 2, '.', ''); ?>
                </td>
            </tr>
            <tr>
                <th colspan="3">Net Total:</th>
                <td>
                    <?php
                    $net = $total_income - $total_expense;
                    $prefix = ($net < 0) ? "-$" : "$";
                    echo $prefix . number_format($net, 2, '.', '');
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>