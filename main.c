int main()
{
    InitializePeripherals();
    XTmrCtr_SetHandler(&TimerInstance, TimerInterruptHandler, &TimerInstance);

    while (1)
    {
        if (Timer4msFlag)
        {
            Timer4msFlag = 0;
            DisplayTime();
        }
        if (Timer1sFlag)
        {
            Timer1sFlag = 0;
            UpdateTime();
            SendTimeOverUART();
        }
    }
    return 0;
}
