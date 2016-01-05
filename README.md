instant-win
===========

PHP class for randomly awarding a fixed amount of instant-wins over a set period
of time.

This project extends the original project at https://github.com/konrness/instant-win 
by including a simulator script to test the behaviour of the algorithm over a
(simulated) extended time period. It also makes explicit some algorithm parameters
that were implicitly defined in the original code.

Thank you to [Korness](https://github.com/konrness) for first writing the algorithm!

Refer to https://github.com/coccoinomane/instant-win for the latest version.

# Usage

See ```scripts/example-daily.php``` for an example of how to award 10 instant-wins each day (midnight to midnight), even when the 
number of instant-win attempts is variable or unknown.

```
$ ./scripts/example-daily.php
You won!!!
$ ./scripts/example-daily.php
Sorry, you did not win.
```
