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
            $transactions = translate_array(get_transactions(FILES_PATH));
            if ($transactions === false) {
                return;
            }

            $total_income = 0;
            $total_expense = 0;
            foreach ($transactions as $transaction) {
                // RENDER
                echo "<tr>\n";
                echo "<td>" . format_date($transaction['Date']) . "</td>\n";
                echo "<td>" . $transaction['Check #'] . "</td>\n";
                echo "<td>" . $transaction['Description'] . "</td>\n";
                $classname = $transaction['Amount'] < 0 ? "negative" : "positive";
                echo "<td class=\"$classname\">" . format_dollar_amount($transaction['Amount']) . "</td>\n";
                echo "</tr>\n";

                // COUNT INCOME AND EXPENSE
                if ($transaction['Amount'] < 0) {
                    $total_expense += abs($transaction['Amount']);
                } else {
                    $total_income += $transaction['Amount'];
                }
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